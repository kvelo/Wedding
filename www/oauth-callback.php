require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('1030174351029-jegtlqamlr01m4aqq3vf7staca27o1vl.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://www.zoevin2025.com/oauth-callback.php');
$client->addScope('https://www.googleapis.com/auth/photoslibrary.readonly');

$auth_url = $client->createAuthUrl();
header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));

// Récupère le code envoyé par Google
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (isset($token['access_token'])) {
        $accessToken = $token['access_token'];

        // Initialise un client HTTP avec ce token
        $photosClient = new GuzzleHttp\Client([
            'base_uri' => 'https://photoslibrary.googleapis.com/',
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
            ]
        ]);

        // Appelle l’API pour récupérer les 10 dernières photos
        $response = $photosClient->post('v1/mediaItems:search', [
            'json' => [
                'pageSize' => 10
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        echo "<h1>Mes dernières photos :</h1>";
        if (!empty($data['mediaItems'])) {
            foreach ($data['mediaItems'] as $item) {
                echo "<div style='margin-bottom:20px;'>";
                echo "<img src='" . htmlspecialchars($item['baseUrl']) . "=w400' style='max-width:100%; height:auto;'/><br>";
                echo "<small>" . htmlspecialchars($item['filename']) . "</small>";
                echo "</div>";
            }
        } else {
            echo "Aucune photo trouvée.";
        }

    } else {
        echo "Erreur lors de la récupération du token : ";
        print_r($token);
    }

} else {
    echo "Aucun code reçu depuis Google.";
}
?>
