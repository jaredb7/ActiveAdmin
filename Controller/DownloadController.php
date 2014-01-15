<?php
App::uses('AppController', 'Controller');
//App::uses('Json', 'View');
//App::uses('Xml', 'View');
//App::uses('CsvView', 'ActiveAdmin.View');

class DownloadController extends ActiveAdminAppController
{
    public $components = array('Paginator', 'RequestHandler', 'ActiveAdmin.Filter');
//    public $helpers = array('Html');
    public $uses = array();

    /**
     * Data exporter for downloads
     * @param string $type
     */
    public function admin_export($type = "csv")
    {
        //Don't auto render the view
        $this->autoRender = false;
        //Remove the layout
        $this->layout = false;

        $model_name = $type = array();
        $filter_via_param = array();
        $date = date('Y-m-d');

//        Testurk: /admin/export/model:trips/type:csv?start_desc=central&created%5BBETWEEN%5D%5B0%5D=&created%5BBETWEEN%5D%5B1%5D=

        //Look at the URL named params to work out what model to use to export data from
        if (array_key_exists('named', $this->params->named)) {
            //Get the controller name
            $controller_name = $this->params->named['named']['c'];
            $dirty_model_name = $this->params->named['named']['c'];
            //What export type was selected
            $type = $this->params->named['model']['type'];
        } else {
            //Get the controller name, it;l be the same as the model name
            $controller_name = $this->params->named['c'];
            $dirty_model_name = $this->params->named['c'];
            //What export type was selected
            $type = $this->params->named['type'];
        }

        //Build the actual model name
        $clean_model_name = Inflector::camelize(Inflector::singularize($dirty_model_name));
        $model_name = $clean_model_name;

        //Set the controller name and model names
        $this->name = Inflector::camelize($controller_name);
        $this->uses = array(0 => $model_name);

        //Exclude any model relationships
        $this->{$model_name}->recursive = -1;
        //Get the model alias
        $model_alias = $this->{$model_name}->alias;

        //Run the filter
        $filter = $this->Filter->process($this);

        //Pagination limit to avoid memory exhaustion on large data sets
        $this->Paginator->settings = array(
            'page' => 1,
            'limit' => 1000,
            'maxLimit' => 1000);

        //Get the data, don't store the result
        $this->Paginator->paginate($model_name, $filter);

        // $this->request['paging'] is now set, read it looks something like this.
        //      'page' => (int) 1,
        //		'current' => (int) 20,
        //		'count' => (int) 40928,
        //		'prevPage' => false,
        //		'nextPage' => true,
        //		'pageCount' => (int) 2047,

        //Generate the file download filename
        $download_filename = $model_alias . '-' . $date . '.' . $type;

        //File as download
        if (Configure::read('ActiveAdmin.allow_downloads') == true) {
            //Get the appropriate content type for the type of export we're doing
            $content_type = $this->response->type($type);

            //A little bit hack and goes against CakePHP methodology
            header('Content-Type: ' . $content_type);
            header('Content-Disposition: attachment; filename="' . $download_filename . '"');

            //We'll write a temp local file to avoid holding everything in memory.
            //Get page count and current page
            $current_page = $this->request['paging'][$model_name]['page'];
            $page_count = $this->request['paging'][$model_name]['pageCount'];

            //Changed to true once the header for the file type $type has been added
            $header_added = false;

            //Open the file
            $outStream = fopen('php://output', 'w');

            //Loop over the number of pages in the pagination
            for ($i = 1; $i <= $page_count; $i++) {
                $this->Paginator->settings = array(
                    'page' => $i,
                    'limit' => 1000,
                    'maxLimit' => 1000,);

                //Get data again the new pagination settings
                $new_responses = $this->Paginator->paginate($model_name, $filter);

                //clean up the response and flatten the array so CsvView can process it
                foreach ($new_responses as $id => $response) {
                    $response_data = $response[$model_alias];
                    //remove the model alias key by putting the underlying data under the numerical index
                    $new_responses[$id] = $response_data;
                }

                //Count the number of responses
                $response_count = count($new_responses);
                $loop_count = 0;

                //Loop over responses, parsing each result
                foreach ($new_responses as $id => $row) {
                    //If requested type is csv
                    if ($type == 'csv' || $type == "CSV") {
                        //Build the header if this is page 1 and the very first entry
                        if ($current_page == 1 && $id == 0 && $header_added == false) {
                            fputcsv($outStream, array_keys($row), ',', '"');
                            $header_added = true;
                        }
                        //Punch out the row data into csv format
                        fputcsv($outStream, array_values($row), ',', '"');

                    } else if ($type == 'json' || $type == "JSON") {
                        //Build the header if this is page 1 and the very first entry
                        if ($current_page == 1 && $id == 0 && $header_added == false) {
                            //Start the array of elements?
                            fputs($outStream, "[");
                            $header_added = true;
                        }

                        //Open
                        fputs($outStream, '{"' . $model_alias . '":');
                        //Build json body
                        fputs($outStream, json_encode($row, JSON_PRETTY_PRINT));
                        //Close
                        fputs($outStream, '}');

                        //close up the json string if were on the page page, and we're on the last record
                        if (($i == $page_count) && $loop_count == ($response_count - 1)) {
                            //Close the array at the very end
                            fputs($outStream, "]");
                        } else {
                            //else add a comma to start the next element
                            fputs($outStream, ', ');
                        }
                        //{"Trip":{"id":"40","timetable_id":"1","trip_direction_id":"1","trip_sequence":"1","run_number":"3532657","route_code_id":"3","start_time":"04:50:00","start_desc":"Bowen Hills station","start_platform":"platform 1","end_time":"06:17:00","end_desc":"Varsity Lakes station","end_platform":"platform 1","express":null,"friday_run":null,"service_status":null,"upcomming_change":null,"created":"2013-08-07 21:46:19","updated":"2013-08-07 21:46:19"}}
                    } else if ($type == 'xml' || $type == "XML") {
                        //Build the xml header if this is page 1 and the very first entry
                        if ($current_page == 1 && $id == 0 && $header_added == false) {
                            fputs($outStream, "<?xml version=\"1.0\" encoding=\"utf-8\"?>");
                            fputs($outStream, "\n");
                            fputs($outStream, "<" . Inflector::pluralize($model_alias) . ">");
                            fputs($outStream, "\n");
                            $header_added = true;
                        }

                        //Build child element, indent it
                        fputs($outStream, "\t<" . $model_alias . '>');

                        //loop over the row and spit out all the elements
                        foreach ($row as $index => $data) {
                            fputs($outStream, "\n");
                            //indent the child attributes
                            fputs($outStream, "\t\t<" . $index . '>' . h($data) . '</' . $index . '>');
                        }

                        //Put a new line so the closing tag is on a line by itself
                        fputs($outStream, "\n");
                        //close the child element
                        fputs($outStream, "\t</" . $model_alias . '>');
                        //new line after the closing element
                        fputs($outStream, "\n");

                        //close up the xml string if were on the page page, and we're on the last record
                        if (($i == $page_count) && $loop_count == ($response_count - 1)) {
                            //Close the array at the end of our round
                            fputs($outStream, "</" . Inflector::pluralize($model_alias) . ">");
                        }
                    }

                    //Increment the loop count
                    $loop_count++;
                }

                //Update the page count and current page
                $page_count = $this->request['paging'][$model_name]['pageCount'];
                $current_page = $this->request['paging'][$model_name]['page'];
            }

            //Close the file
            fclose($outStream);
        }
    }
}