<?php
//
//Create the class to extract the data from the specified database on the remote
//server and display the data.
//Extracts data from a specified server
class extractor{
    //
    //The database name (on the local server) to extract the data from
    public string $dbname;
    //
    //The credentials for the server 
    public  array/*{url, username, password}*/$servers;
    //
    function __construct(string $dbname, array $server){
        $this->dbname = $dbname;
        $this->servers = $server;
    }
    
    //Execute the array of sqls to extract desired data from te lcal databse
    function execute(array/*{tname:string, sql:string}*/ $sqls):array /*<ingester>*/{
        //
        //Create a pdo (data connection) using the extractors dbname
        //
        //These credentials are for link to which server ?.
        //
        $dsn = "mysql:host=localhost;dbname=".$this->dbname;
        //
        $username = $this->servers['username'];
        //
        $password = $this->servers['password'];
        //
        //Now create the PDO
        $pdo = new \PDO($dsn, $username, $password);
        //
        //Use the PDO to compile teh desireed data
        return array_map(fn($sql)=> self::get_data($sql, $pdo), $sqls);
    }

    //The desired data is a matrix correspiknding to a the sql , plus all
    //the columns (as a pair of ename anc cname), that make up the sql
    function get_data($sql/*{tname:string, sql:string}*/, \PDO $pdo):array /*<{tname, matrix, columns}>*/{
        //
        //Run the query to get a pdo statement
        $stmt = $pdo->query($sql['sql']);
        // 
        //Fetch the result as a  simple matrix, i.e, Array<Array<basic_value>>
        $matrix = $stmt->fetchAll(\PDO::FETCH_NUM);
        //
        //Use the PDO statement to get the column metadata and map the metadata
        // to columns, i.e., ename/cname pairs
        $columns = $this->get_column_meta_data($stmt);
        //
        return ['tname'=>$sql['tname'], 'matrix'=>$matrix, 'columns'=>$columns];
    }
    //
    //Get the column meta data from a PDOStatement. And extract the column names.
    function get_column_meta_data(\PDOStatement $stmt):array /*<{ename:string, cname: string}>*/ {
        //
        //Start with an empty array
        $result = [];
        //
        // get the column count from the statement
        $count = $stmt->columnCount();
        //
        //Loop through the count and for each get the column data;
        for ($col = 0; $col < $count; $col++){
            //
            //Get the column data. This is an array.
            $columns_meta= $stmt->getColumnMeta($col);
            //
            //Get the table name from the meta data.
            $ename= $columns_meta['table'];
            //
            //From the column meta data, extract the column names.
            $column = $columns_meta['name'];
            //
            $cname = $this->ed_columns($column, $ename);
            //
            array_push($result, ['ename'=>$ename, 'cname'=>$cname]);
        }
        //
        return $result; /*Array<{ename, cname}*/
    }
    
    function ed_columns(string $column,string $ename):string{
        //
        //Find the word with the following syntax table_.
        $rep = $ename."_";
        //
        //Replace the word with an empty value.
        $val = '';
        //
        //Return the column name only.
        return str_replace($rep, "", $column);
    }
}
  

//
//create a new extractor with the following parameters.
// - the database name. as a string,
// - the url, username, password as an array.
$extract = new extractor($dbname, $server);
//
//Execute the above.
$result = $extract->execute();
//
//Show the final output.
echo $result;