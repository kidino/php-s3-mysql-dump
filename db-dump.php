<?php
set_time_limit(120);
include('S3.php');
include('config.php');


// Starts S3
S3::setAuth(ACCESS_KEY,SECRET_KEY);

// Connects to database 
$link = mysql_connect(DB_SERVER,DB_USER,DB_PASS);
mysql_select_db(DB_DATABASE,$link);

// Stages tables to dump
if($tables == '*'){
	$tables = array();
	$result = mysql_query('SHOW TABLES');
	while($row = mysql_fetch_row($result)){
		$tables[] = $row[0];
	}
}else{
	$tables = is_array($tables) ? $tables : explode(',',$tables);
}

// Loop through tables 
foreach($tables as $table){
	$result = mysql_query('SELECT * FROM '.$table);
	$num_fields = mysql_num_fields($result);
	
	if(DUMP_TYPE == 'TABLES'){
		$return = 'DROP TABLE IF EXISTS '.$table.';';
	} elseif(DUMP_TYPE == 'DATABASE'){
		$return .= 'DROP TABLE IF EXISTS '.$table.';';
	}
	$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
	$return.= "\n\n".$row2[1].";\n\n";
	
	for ($i = 0; $i < $num_fields; $i++){
		while($row = mysql_fetch_row($result)){
			$return.= 'INSERT INTO '.$table.' VALUES(';
			for($j=0; $j<$num_fields; $j++){
				$row[$j] = addslashes($row[$j]);
				$row[$j] = preg_replace("#\n#","\\n",$row[$j]);
				if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
				if ($j<($num_fields-1)) { $return.= ','; }
			}
			$return.= ");\n";
		}
	}
	$return.="\n\n\n";
	
	if(DUMP_TYPE == 'TABLES'){
		$dump_name = DUMP_PREFIX.$table.'.sql';
		$handle = fopen($dump_name,'w+');
		fwrite($handle,$return);
		
		fclose($handle);
		
		
		if(S3::putObjectFile($dump_name,S3_BUCKET,S3_URI.$dump_name,S3::ACL_PRIVATE)){
			print $dump_name . " dumped to S3<br />";
			unlink($dump_name);
		}
	}
}

if(DUMP_TYPE == 'DATABASE'){
	$dump_name = DUMP_PREFIX.$table.'.sql';
	$handle = fopen($dump_name,'w+');
	fwrite($handle,$return);

	fclose($handle);
		
	if(S3::putObjectFile($dump_name,S3_BUCKET,S3_URI.$dump_name,S3::ACL_PRIVATE)){
		print $dump_name . " dumped to S3<br />";
		unlink($dump_name);
	}
}

?>