<?php
ini_set('max_execution_time', 0);
ini_set('memory_limit', '-1');
require_once('simplehtmldom_1_5/simple_html_dom.php');



/*if ($file !== false) {
  fwrite($file, 'message to be written' . "\n");
  fclose($file);
}*/

$i=0;$keyword = ""; $location = "";$linkObjs=[];$items=[];
if(isset($_POST['submit'])) {
  $keyword = $_POST['keyword'];

  //Save to file.
  //$file = fopen("parsed-data_".date('Y_m_d_h_i_s').".csv","w");
  $file = fopen("fittingo-data.csv","w");
  if ($file === false) {
    throw new Exception ("Failed! File was not created.");
  }

  $url  = 'http://www.fittingo.com/Kalori_Cetveli.aspx/FoodList';

  $query = array($url, array("ProductName" => $keyword));
  $html_str = getSslPage($query);
  $json_data = json_decode($html_str);
  $json_data = json_decode($json_data->d);

  //print_r($json_data); die;
  $status = $json_data->durum;
  $list = $json_data->Liste;
  //$html = file_get_html($url);

  if(strlen($list) > 0) {
    $html = str_get_html($list);

    //Parse data
    $linkObjs = $html->find('span');
    $p_ids = $html->find('li');

  }


}

echo "
<!DOCTYPE html>
<html>
<head>
  <title>Fittingo</title>
  <link href='style.css' rel='stylesheet' type='text/css'>
</head>

<body>";


echo "<h1>Search from Fiitingo.com</h1>";
echo "
<form method='post'>
<input type='text' maxlength='4' name='keyword' value='" .(!empty($keyword)? $keyword : ""). "' placeholder='keyword'>
<input type='submit' value='Search' name='submit'>
</form>";



echo "<h5>" .count($linkObjs). " Results found "
. ((count($linkObjs) > 0)? "" : "") . "</h5>
<table class='qa-comment-list'>
";

foreach ($linkObjs as $key => $linkObj) {

    echo "<tr>";
    echo "<td>";
    echo $title = $linkObj->text();
    $id = $p_ids[$key]->attr['id'];
    echo "</td>";
    echo "<td>";

    $url  = 'http://www.fittingo.com/Kalori_Cetveli.aspx/FoodDetails';
    $query = array($url, $id);
    $html_str = getDetails($query);
    $json_data = json_decode($html_str);
    $json_data = json_decode($json_data->d);

    echo "<h3>100 gr $title</h3>";
    echo "<table border='1'>
        <tr>
        <td>Kalori: </td>
        <td>$json_data->Kalori</td></tr><tr>

        <td>Karbonhidrat: </td>
        <td>$json_data->Karbonhidrat</td></tr><tr>

        <td>Protein: </td>
        <td>$json_data->Protein</td></tr><tr>

        <td>Yağ: </td>
        <td>$json_data->Yag</td></tr>
</table>";
    echo "</td>";
    echo "</tr>";
    //fwrite($file, $content);
}

//Close the file pointer;
if(isset($_POST['submit']))
  fclose($file);
?>
<?php

function getSslPage($query) {

  $data = $query[1];
  $data_string = json_encode($data);

  $ch = curl_init($query[0]);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Content-Length: ' . strlen($data_string))
  );

  $result = curl_exec($ch);

  return $result;

}

function getDetails($query) {

  $data = $query[1];
  $data_string = json_encode(array("FoodId" => $query[1]));;

  $curl = curl_init($query[0]);
  curl_setopt($curl, CURLOPT_HEADER, false);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_HTTPHEADER,
    array("Content-type: application/json"));
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

  $json_response = curl_exec($curl);

  curl_close($curl);

  return $json_response;
}
?>
<?php

  echo "
</table>
  </body>
  </html>
  ";

?>

