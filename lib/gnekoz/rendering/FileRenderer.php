<?php
namespace gnekoz\rendering;
use gnekoz\rendering\Renderer;

/**
 * @author gneko
 *
 */
class FileRenderer implements Renderer
{

  /**
   *
   */
  public function getContentType() {
    // TODO: Auto-generated method stub
  }

  /**
   * @param unknown $data
   */
  public function render($data)
  {
  	//var_dump($data); exit();
    $file = $data['file'];
    $name = $data['name'];
    $finfo = new \finfo(FILEINFO_MIME);
    $mime = $finfo->buffer(file_get_contents($file));
    
    //echo "x : " .filesize($file); exit();
       
    //$mime = $info->file($file, FILEINFO_MIME_TYPE);
    header_remove();
    header('Content-Description: File Transfer');
	//header('Content-Type: application/octet-stream');
    header("Content-Type: $mime");
    header("Content-Disposition: attachment; filename=$name");
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    //var_dump($mime); exit();
    readfile($file);
  }

}
