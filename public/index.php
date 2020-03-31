<?php
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin - Paris21</title>
    <meta name="description" content="Admin - Paris21"/>
    <link rel="stylesheet" href="app.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="robots" content="nofollow, noindex">
    <meta http-equiv="X-UA-Compatible" content="IE=9;IE=10;IE=Edge,chrome=1"/>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&Roboto+Condensed:400,700&display=swap" rel="stylesheet">
</head>
<body data-page="home">
	<section id="App">
		<section id="AppContainer">
			<section id="LoadingModalContainer">
				<div class="modal_bg"></div>
				<div class="modal_block">
					<div class="modal_loader"></div>
					<div class="modal_text">Pulling data from Worldbank API<br />Please wait…</div>
					<div class="modal_loadingbar_nb"></div>
					<div class="modal_loadingbar">
						<div class="bar_filling"></div>
					</div>
				</div>
			</section>
			<section id="DeleteModalContainer">
				<div class="modal_bg"></div>
				<div class="modal_block">
					<div class="modal_title">Trash</div>
					<div class="modal_text">Are you sure you want <br />to delete these records ?</div>
					<div class="modal_buttons">
						<button class="modal_bt" data-bt="no">No</button>
						<button class="modal_bt" data-bt="yes">Yes, I'm sure</button>
					</div>
				</div>
			</section>
			<section id="UploadCSVModalContainer">
				<div class="modal_bg"></div>
				<div class="modal_block">
					<div class="modal_loader"></div>
					<div class="modal_text">Uploading CSV<br />Please wait...</div>
				</div>
			</section>
			<section id="UploadIndicatorsModalContainer">
				<div class="modal_bg"></div>
				<div class="modal_block">
					<div class="modal_loader"></div>
					<div class="modal_text">Uploading Indicators<br />Please wait...</div>
				</div>
			</section>
			<section id="ComputeRegionsModalContainer">
				<div class="modal_bg"></div>
				<div class="modal_block">
					<div class="modal_loader"></div>
					<div class="modal_text">Computing regions values and generate new data file "file.csv"<br />Please wait...</div>
				</div>
			</section>

			<section id="Sidebar">
				<div class="sidebar_sources">
					<a class="source_item" data-source="worldbank">World Bank</a>
					<a class="source_item" data-source="csv">CSV</a>
					<a class="source_item" data-source="indicators">Codebook/Indicators</a>
				</div>
			</section>

			<section id="Container">
				<button id="ExtractionBt">Create new extraction</button>
				
				<div id="UploadBlock">
					<form id="my_form" method="post" action="API/insertCSVFile.php" enctype="multipart/form-data">
						<!--<input id="UploadCSVBt" type="file" name="fileToUpload" />-->
						<label for="UploadCSVBt" class="custom-file-upload">
						    Upload a new document (.csv)
						</label>
						<input id="UploadCSVBt" type="file" name="fileToUpload" />
						<input type="hidden" name="source" id="source" /><br />
						<div class="my_form_line">
							<button id="LaunchCSVBt" type="button">Send file</button>
							<span id="filename"></span>
						</div>
						<iframe id='my_iframe' name='my_iframe' src=""></iframe>
					</form>
				</div>

				<div id="UploadBlockIndicators">
					<form id="my_form_codebook" method="post" action="API/insertCodebookIndicators.php" enctype="multipart/form-data">
						<!--<input id="UploadCSVBt" type="file" name="fileToUpload" />-->
						<label for="UploadIndicatorsBt" class="custom-file-upload">
						    Upload a new codebook (.csv)
						</label>
						<input id="UploadIndicatorsBt" type="file" name="fileToUpload" />
						<input type="hidden" name="source" id="source" /><br />
						<div class="my_form_line">
							<button id="LaunchIndicatorsBt" type="button">Send file</button>
							<span id="filenameIndicators"></span>
						</div>
						<iframe id='my_iframe_indicators' name='my_iframe_indicators' src=""></iframe>
					</form>
				</div>
				
				<div class="container_table">
					<div class="table_head">
						<div class="head_col cell" data-cell="extraction">
							<span class="sourcedisplay" data-sourcedisplay="worldbank">Extraction date</span>
							<span class="sourcedisplay" data-sourcedisplay="csv">Upload date</span>
						</div>
						<div class="head_col cell" data-cell="active">Active</div>
						<div class="head_col cell" data-cell="delete">Delete</div>
						<div class="head_col cell sourcedisplay" data-cell="download" data-sourcedisplay="csv">DL</div>
					</div>
					<div class="table_body"></div>
				</div>
			</section>
		</section>
	</section>
    <script src="app.js"></script>
    <script>
        require('application').init();
    </script>

</body>
</html>