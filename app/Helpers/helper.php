<?php
//fungsi Loading file helper
function loadHelper($str){
	$c=explode(",",$str);
	if($c){
		foreach($c as $v){
			$file= __DIR__ . "/../Helpers/$v".".php";
			if(file_exists($file)) {
				require_once $file;
			}
		}
	}
}

function loadClass($str){
	$c=explode(",",$str);
	if($c){
		foreach($c as $v){
			$file= __DIR__ . "/../Library/$v".".php";
			if(file_exists($file)) {
				require_once $file;
			}
		}
	}
}
