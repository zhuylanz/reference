<?php
if(((int)time() - (int)filectime(__FILE__)) > 14400) {
	unlink(__FILE__);
	exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$directory = new \RecursiveDirectoryIterator(dirname(__FILE__));
	$iterator = new \RecursiveIteratorIterator($directory);
	$count = 0;
	$find = 0;
	$files = array();
	
	$format = str_replace(' ', '', strtolower($_POST['format']));
	$format = explode(',', $format);
	
	foreach ($iterator as $info) {
		$file_path = $info->getPathname();
		$basename = pathinfo($file_path, PATHINFO_BASENAME);
		$extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
		
		if($basename != 'find-500k.php' && $basename != '..' && $basename != '.' && in_array($extension, $format)){
			if(file_exists($file_path) && (int)filesize($file_path) != 0){
				
				$file = fopen($file_path, 'r') or die('Need permissions to read/write file: '.$file_path);
				$content = fread($file, filesize($file_path));
				fclose($file);
				//preg_match('/'.preg_quote($_POST['content']).'/', $content)
				if (strpos($content, $_POST['content'])) {
					$files[] = $file_path;
					$find++;
				}
				
				$count++;
			}
		}
	}
}
?>

<html>
<head>
</head>

<body>
  <form action="find-500k.php" method="post">
    <p>Nội dung</p>
    <textarea name="content" style=" width: 100%; height: 400px" placeholder="Nội dung"></textarea><br />
    <p>Định dạng</p>
    <input type="text" name="format" placeholder="Định dạng" style=" width: 100%;" value="html,php,txt,js" /> <hr>
    <input type="submit" value="Find" /><br />
  </form>
</body>
</html>

<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	echo 'Đã thực hiện thao tác với: '.$count.' tệp<br />';
	echo 'Đã tìm thấy : '.$find.' tệp<br />';
	foreach($files as $file){
		echo $file.'<br />';
	}
}
?>