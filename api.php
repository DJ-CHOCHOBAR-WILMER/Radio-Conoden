<?php

//API SONIC PANEl
$url = "https://radio.livestreamingmundial.com/cp/get_info.php?p=8078";
$ch = curl_init();
curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
$return_json = curl_exec($ch);

$obj = json_decode($return_json);
$tit = $obj->{'title'};
$tit2 = preg_replace('/\W+/', '%20', $tit);
$unique_listeners = $obj->{'ulistener'};
$online_listeners = $obj->{'listeners'};

// Funcion para obtener imagen desde API de itunes
function getArtwork($text) {
  $url = @file_get_contents('https://itunes.apple.com/search?term=' . $text . '&media=music&limit=1');
  $json = json_decode($url, true);
  foreach ($json['results'] as $value) {
    $foto = $value['artworkUrl100'];
  }
  if (empty($foto)) {
    $imagen = "_img/nocover.png";
  } else {
    $imagen = preg_replace('/100x100bb/', '1500x1500bb', $foto);
  }
  return $imagen;
}

//api Portada o album artista
$imagen = getArtwork($tit2);

$played_last20 = $obj->{'history'};

// Objeto para almacenar historial de canciones.
$history = array();

foreach($played_last20 as $tracks) { 
  // Remover etiquetas html
  $tracks = strip_tags($tracks);
  // Remueve espacios en blanco y el numero al inicio de la cadena que tiene el formato "1.) "
  $tracks = preg_replace('/^\d+\.\)\s/', '', $tracks);

  $history[] = array(
    "song" => $tracks
  );
}

?>

{
  "type":"result",
  "data":[
    {
      "song":"<?= $tit ?>",
      "image":"<?= $imagen ?>",
      "listeners":"<?= $online_listeners ?>",
      "unique_listeners":"<?= $unique_listeners ?>",
      "history": <?= json_encode($history) ?>
    }
  ]
}