<?php
//
/*
type ingester = 
        {
            //the table name where the data is extracted from.
            tname:string,
            //
            //The data received from the execution of the query on the table above.
            matrix: matrix = Array<Array<basic_value>>,
            //
            //The column names to support the formulating of labels to save 
            //the data to the database.
            columns: subject = {ename:string, cname: string}
        }
    
*/
//
//This is a class to support the extraction, transmission and ingestion of data 
//from one server to another. Regardless of the database.
class migration {
    //
    //The database to get data from?.
    public $dbname;
    //
    //This is the expression used to get data from the given table and column.
    const LOOKUP = "mutall\\capture\\lookup";
    //
    function __construct($dbname) {
        //
        //Get the database name.
        $this->dbname = $dbname;
    }
    //
    //Set up the triggers to collect the changes in the co.ke server.
    function set_collectors():void{

    }
    //
    //Extract the data collected by the ingester above in the form of an ingester.
    function extract()/*ingesters*/{
        //
        //Create the tables as a list.
        //
        //Get all the sqls.
        //
        //Create a pdo statement.
        //
        //Get the column names from the sql..
        //
        //create table layouts.
    }
    //
    //Transmit the data extracted from the ingester above.
    function transmit()/*ingesters */{

    }
    //
    //
    //Put the given data (extracted from the changes table) from mutall.co.ke to the 
    // mutall rental database specific tables in the (do) server.
    function ingest(
        //
        //The data recieved as a json string; it has no columns.
        array /*<ingester>*/ $ingesters
    ):string /*ok | errors*/{
        //
        //
        //1. Create the layouts from the ingester.
        $layouts = [...get_layout($ingesters)];
        //
        //2. Create a new questionnaire to load the given layouts.
        $q = new mutall\questionnaire($layouts);
        //
        //3. Load the layouts.
        $result/*ok | errors*/  = $q->load_common();
        //
        //4. Return the result.
        return $result;
    }
    //
    //Generate the layouts for loading the data extracted from mutall.co.ke (server) to
    //mutallco rental database in (do) server.
    function get_layout(array $ingesters):\Generator{
        //
        //Loop through all the ingester elements and yield a table and its 
        //corresponding labels
        foreach($ingesters as $ingester){
            //
            //Extract the table name from the ingester,...
            $tname /*string */ = $ingester->tname;
            // 
            // column names as subjects,...
            $columns /*{ename:string, cname: string}*/ = $ingester->columns;
            // 
            // and the matrix from the ingester above.
            $matrix /*basic_value[][]*/ = $ingester->matrix;
            //
            //Yield the table layout.
            yield get_table($tname, $columns, $matrix);
            //
            //Yield the labels; there will be as many as there are columns in the table.
            yield from get_labels($tname, $columns);
        }
    }
    /*
    //Yield the table of type layout where the source of the data is a matrix. 
    It has the following structure.
    {
        classname: "mutall\\capture\\matrix",
        args : [
            //
            //The table's name, used in formulating lookup expressions    
            tname: string,
            //
            //The table's header as an array of colum names indexed by their 
            //positions     
            cnames:  Array<cname>
            //    
            //A tables fueuel, as a double array of basic values    
            ifuel: matrix = Array<<Array<basic_value>>>,
            //
            //Where does the body start    
            body_start:int = 0
        ]
    }*/
    function get_table(string $tname,array /*<subject ={ename, cname}>*/ $columns, array/*matrix*/$matrix):\Generator {
        //
        // Create a new standard object.
        $table = new \stdClass();
        //
        $table->classname = "mutall\\capture\\fuel";
        //
        $table->args = [
            //
            //Get the table name.
            $tname,
            //
            //Get the column names as an array
            array_map(fn($column) => $column->cname , $columns),
            //
            //Get the data.
            $matrix
        ];
        //
        yield $table;
    }
    //
    //Yield the labels for the columns to support saving of the data, to the database specific tables.
    function get_labels(string $tname,array /*<subject ={ename, cname}>*/ $columns): \Generator {
        //
        //Formulate the labels from the column names;
        foreach ($columns as $col) {
            //
            //Get the entity name from the column.
            $ename = $col->ename;
            //
            //Get the column name from the column.
            $cname = $col->cname;
            //
            //Generate the label
            yield [$this->dbname, $ename, [$tname], $cname, [self::LOOKUP, $tname, $cname]];
        }
    }
}