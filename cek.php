<?php
require_once 'vendor/autoload.php';
require_once "./random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=westorage91;AccountKey=IFHHjQqz0MaxqLFEcovniXy/7uwX6iw3/LzCCsTC1eUS9ZGoBP3VTCt7CBx32pBw546fbTHfGestiHd8QAr3QQ==;";
// Membuat blob client.
// $blobClient = BlobRestProxy::createBlobService($connectionString);
 
// # Membuat BlobService yang merepresentasikan Blob service untuk storage account
// $createContainerOptions = new CreateContainerOptions();

// $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);

// // Menetapkan metadata dari container.
// $createContainerOptions->addMetaData("key1", "value1");
// $createContainerOptions->addMetaData("key2", "value2");

$containerName = "irfanblobs";
 
// Create blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);
if (isset($_POST['submit'])) {
	$fileToUpload = strtolower($_FILES["fileToUpload"]["name"]);
	$content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
	// echo fread($content, filesize($fileToUpload));
	$blobClient->createBlockBlob($containerName, $fileToUpload, $content);
	header("Location: cek.php");
}
$listBlobsOptions = new ListBlobsOptions();
$listBlobsOptions->setPrefix("");
$result = $blobClient->listBlobs($containerName, $listBlobsOptions);
?>

<!DOCTYPE html>
<html>
 <head>
 <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    
    <title>Deteksi Gambar</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/starter-template/">

    <!-- Bootstrap core CSS -->
    <link href="https://getbootstrap.com/docs/4.0/dist/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" />
 

    <!-- Custom styles for this template -->
    <link href="starter-template.css" rel="stylesheet">
  </head>
<body>
	<nav class="navbar navbar-expand-md bg-light navbar-light  fixed-top">
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarsExampleDefault">
			<ul class="navbar-nav mr-auto">
		  <li class="nav-item">
				<a class="nav-link" href="cek.php">Upload Gambar</a>
			</li>
            <li class="nav-item">
				<a class="nav-link" href="tes.php">Analisis Gambar</a>
			</li>
		
		</div>
		</nav>
		<main role="main" class="container">
    		<div class="starter-template"> <br><br><br>
        		<h1>Analisis Gambar</h1>
				<p class="lead">Pilih Foto  Anda.<br> Kemudian Click <b>Upload</b>, untuk menganalisa foto pilih <b>cek</b> pada tabel.</p>
				<span class="border-top my-3"></span>
			</div>
		<div class="mt-4 mb-2">
			<form class="d-flex justify-content-lefr" action="cek.php" method="post" enctype="multipart/form-data">
				<input type="file" name="fileToUpload" accept=".jpeg,.jpg,.png" required="">
				<input type="submit" name="submit" value="Upload">
			</form>
		</div>
		<br>
		<br>
		
		<table class='table table-hover' id="tabel-data">
			<thead>
				<tr>
					<th>Name</th>
					<th>URL</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php
				do {
					foreach ($result->getBlobs() as $blob)
					{
						?>
						<tr>
							<td><?php echo $blob->getName() ?></td>
							<td><?php echo $blob->getUrl() ?></td>
							<td>
								<form action="tes.php" method="post">
									<input type="hidden" name="url" value="<?php echo $blob->getUrl()?>">
									<input type="submit" name="submit" value="Cek!" class="btn btn-primary">
								</form>
							</td>
						</tr>
						<?php
					}
					$listBlobsOptions->setContinuationToken($result->getContinuationToken());
				} while($result->getContinuationToken());
				?>
			</tbody>
		</table>

	</div>

<!-- Placed at the end of the document so the pages load faster -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
    <script src="https://getbootstrap.com/docs/4.0/assets/js/vendor/popper.min.js"></script>
    <script src="https://getbootstrap.com/docs/4.0/dist/js/bootstrap.min.js"></script>
		
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
 <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
 <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

 <script>
    $(document).ready(function(){
        $('#tabel-data').DataTable();
    });
</script>
  </body>
</html>