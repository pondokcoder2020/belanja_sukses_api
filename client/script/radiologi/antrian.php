<script src="<?php echo __HOSTNAME__; ?>/plugins/ckeditor5-build-classic/ckeditor.js"></script>
<script type="text/javascript">
	$(function(){
		var uid_order = __PAGES__[2];
		var order_data;
		var forSave = {};
		var selectedState = '';
		var editorKeteranganPeriksa, editorKesimpulanPeriksa;
		var tindakanID;
		var fileList = [];
		var deletedDocList = [];	//for save all file uploaded
		var file;//for upload file

		$("#panel-hasil").hide();

		var dataLibrary = loadOrder(uid_order);

		for(var datKey in dataLibrary) {
		    forSave["tindakan_" + dataLibrary[datKey].id] = {
		        keterangan: (dataLibrary[datKey].keterangan === null) ? "" : dataLibrary[datKey].keterangan,
                kesimpulan: (dataLibrary[datKey].kesimpulan === null) ? "" : dataLibrary[datKey].kesimpulan
            };
        }

		loadPasien(uid_order);
		loadLampiran(uid_order);

		$("#list-tindakan-radiologi tbody tr td").on("click", ".linkTindakan", function(e) {

			let id_tindakan = $(this).parent().parent().attr("id").split("_");
			tindakanID = id_tindakan[id_tindakan.length - 1];


            if(forSave["tindakan_" + tindakanID] === undefined) {
                forSave["tindakan_" + tindakanID] = {
                    keterangan: "",
                    kesimpulan: ""
                };
            }

            if(selectedState != tindakanID) {
                $("#panel-hasil").fadeIn(function() {
                    editorKeteranganPeriksa.setData(forSave["tindakan_" + tindakanID].keterangan);
                    editorKesimpulanPeriksa.setData(forSave["tindakan_" + tindakanID].kesimpulan);
                });
                selectedState = tindakanID;
            }



            let nama =  $(this).closest('tr').find('td:eq(1)').text(); //$(this).html();
            $(".title-pemeriksaan").html(nama);


			return false;
		});



		ClassicEditor
			.create( document.querySelector( ".txt_keterangan_pemeriksaan" ), {
				//plugins : [ Autosave ],
				extraPlugins: [ MyCustomUploadAdapterPlugin ],
				placeholder: "Keterangan Pemeriksaan..."
			})
			.then( editor => {
				editorKeteranganPeriksa = editor;
				window.editor = editor;

                editor.editing.view.document.on( 'keydown', ( evt, data ) => {
                    forSave["tindakan_" + selectedState].keterangan = editorKeteranganPeriksa.getData();
                });
			})
			.catch( err => {
				//console.error( err.stack );
			});


		ClassicEditor
			.create( document.querySelector( ".txt_kesimpulan_pemeriksaan" ), {
				extraPlugins: [ MyCustomUploadAdapterPlugin ],
				placeholder: "Keterangan Pemeriksaan..."
			} )
			.then( editor => {
				editorKesimpulanPeriksa = editor;
				window.editor = editor;

                editor.editing.view.document.on( 'keydown', ( evt, data ) => {
                    forSave["tindakan_" + selectedState].kesimpulan = editorKesimpulanPeriksa.getData();
                });
			} )
			.catch( err => {
				//console.error( err.stack );
			} );


		

		$("#formHasilRadiologi").submit(function(){
			var form_data = new FormData(this);
			form_data.append("request", "update-hasil-radiologi");
			form_data.append("uid_radiologi_order", uid_order);
			
			//if (tindakanID !== undefined && tindakanID !== ""){
				let keteranganPeriksa = editorKeteranganPeriksa.getData();
				let kesimpulanPeriksa = editorKesimpulanPeriksa.getData();
				//console.log(deletedDocList);

				/*formData = {
					request : "update-hasil-radiologi",
					keteranganPeriksa : keteranganPeriksa,
					kesimpulanPeriksa : kesimpulanPeriksa,
					tindakanID : tindakanID
				};*/

				form_data.append("keteranganPeriksa", keteranganPeriksa);
				form_data.append("kesimpulanPeriksa", kesimpulanPeriksa);
                form_data.append("detail", JSON.stringify(forSave));
				form_data.append("tindakanID", tindakanID);

				/*$("#btnSimpan").attr({
					"disabled": "disabled"
				});*/

				for(var i = 0; i < fileList.length; i++) {
					form_data.append("fileList[]", fileList[i]);
				}

				for (var i = 0; i < deletedDocList.length; i++){
					form_data.append("deletedDocList[]", deletedDocList[i]);
				}


				/*for (var value of form_data.values()) {
				   console.log(value); 
				}*/

                for (var pair of form_data.entries()) {
                    //console.log(pair[0]+ ', ' + pair[1]);
                }

				$.ajax({
					async: false,
					url: __HOSTAPI__ + "/Radiologi",
					processData: false,
					contentType: false,
					data: form_data,
					beforeSend: function(request) {
						request.setRequestHeader("Authorization", "Bearer " + <?php echo json_encode($_SESSION["token"]); ?>);
					},
					type: "POST",
					success: function(response){
						let order_detail = 0;
						let response_upload = 0;
						let response_delete_doc = 0;

						/*if (
							response.response_package.order_detail !== undefined && 
							response.response_package.order_detail !== ""
						) {
							order_detail = response.response_package.order_detail.response_result;
						}*/

						if (
							response.response_package.response_upload !== undefined && 
							response.response_package.response_upload !== ""
						) {
							response_upload = response.response_package.response_upload.response_result;
							if (response_upload > 0){
								fileList = [];
								$("#radiologi-lampiran-table tbody").empty();
								loadLampiran(uid_order);
							}
						}
						
						if (
							response.response_package.response_delete_doc !== undefined && 
							response.response_package.response_delete_doc !== ""
						) {
							response_delete_doc = response.response_package.response_delete_doc.response_result;
							if (response_delete_doc > 0){
								deletedDocList = [];
							}
						}

						var detailRes = response.response_package.order_detail;
						for(var resKey in detailRes) {
                            order_detail += detailRes[resKey].response_result;
                        }

						if(order_detail > 0) {
                            notification ("success", "Data Berhasil Disimpan", 3000, "hasil_tambah_dev");
                            location.href = __HOSTNAME__ + "/radiologi";
                        }


						/*if (order_detail > 0 || response_upload > 0 || response_delete_doc > 0){
							notification ("success", "Data Berhasil Disimpan", 3000, "hasil_tambah_dev");
							location.href = __HOSTNAME__ + "/radiologi";
						} else {
						    console.log(response);
							$("#btnSimpan").removeAttr("disabled");
							notification ("warning", "Tidak Ada Data yang Disimpan", 3000, "hasil_tambah_dev");
						}*/
					},
					error: function(response) {
						notification ("danger", "Data Gagal Disimpan", 3000, "hasil_tambah_dev");
						$("#btnSimpan").removeAttr("disabled");
						console.log("Error : ");
						console.log(response);
					}
				});
			//}
			//}

			return false;
		});

		$("#form-upload-lampiran").on("shown.bs.modal", function () {
			if (file.type == "application/pdf" && file != undefined) {
				var fileReader = new FileReader();
				fileReader.onload = function() {
					var pdfData = new Uint8Array(this.result);
					// Using DocumentInitParameters object to load binary data.
					var loadingTask = pdfjsLib.getDocument({
						data: pdfData
					});
					loadingTask.promise.then(function(pdf) {
						// Fetch the first page
						var pageNumber = 1;
						pdf.getPage(pageNumber).then(function(page) {
							var scale = 1.5;
							var viewport = page.getViewport({
								scale: scale
							});
							// Prepare canvas using PDF page dimensions
							var canvas = $("#pdfViewer")[0];
							var context = canvas.getContext("2d");
							canvas.height = viewport.height;
							canvas.width = viewport.width;
							// Render PDF page into canvas context
							var renderContext = {
								canvasContext: context,
								viewport: viewport
							};
							var renderTask = page.render(renderContext);
							renderTask.promise.then(function() {
								//$("#btnSubmit").removeAttr("disabled").html("Terima SK").removeClass("btn-warning").addClass("btn-primary");
							});
						});
					}, function(reason) {
						// PDF loading error
						console.error(reason);
					});
				};
				fileReader.readAsArrayBuffer(file);
			}
		});

		var status_file;

		$("#add_file").change(function(e) {
		
			file = e.target.files[0];
			if (file.name != ""){
				status_file = checkFile(file.name);
			}

			if (status_file == true){
				$("#form-upload-lampiran").modal("show");
			} else {
				alert("Berkas harus PDF");
			}
		});

		$("#btnSubmitLampiran").click(function() {
			autoDocument(file);
			fileList.push(file);
			//check_page_2();
			$("#form-upload-lampiran").modal("hide");
		});

		$("body").on("click", ".delete_document", function() {
			var id = $(this).attr("id").split("_");
			id = id[id.length - 1];
			fileList.splice((id - 1), 1);
			$("#document_" + id).hide();
			rebaseLampiran();
			return false;
		});

		$("#radiologi-lampiran-table tbody").on("click", ".delete_document_registered", function(){
			var id = $(this).data("id").split("_");
			id = id[id.length - 1];

			deletedDocList.push(id);
			$(this).parent().parent().remove();
			rebaseLampiran();
			return false;
		});
	});

	function loadOrder(uid_order){		//uid_radiologi_order
		if (uid_order != ""){
			var MetaData;
			$.ajax({
				async: false,
				url:__HOSTAPI__ + "/Radiologi/get-order-detail/" + uid_order,
				type: "GET",
				beforeSend: function(request) {
					request.setRequestHeader("Authorization", "Bearer " + <?php echo json_encode($_SESSION["token"]); ?>);
				},
				success: function(response){
					MetaData = response.response_package.response_data;

					if (MetaData != "") {
						for(i = 0; i < MetaData.length; i++){
							html = "<tr id=\"tindakan_" + MetaData[i].id + "\">" +
										"<td>" + (i + 1) +"</td>" +
										"<td>" + MetaData[i].tindakan + "</td>" +
										"<td>" + MetaData[i].penjamin + "</td>" +
										"<td>" +
											"<a href=\"#\" class=\"linkTindakan btn btn-sm btn-info\">" +
												"<i class=\"fa fa-eye\"></i>" +
											"</a>" +
										"</td>" +
									"</tr>";

							$("#list-tindakan-radiologi tbody").append(html);
						}
					}
				},
				error: function(response) {
					console.log(response);
				}
			});
			return MetaData;
		}
	}

	function loadPasien(uid_order){		//uid_radiologi_order
		if (uid_order != ""){
			$.ajax({
				async: false,
				url:__HOSTAPI__ + "/Radiologi/get-data-pasien-antrian/" + uid_order,
				type: "GET",
				beforeSend: function(request) {
					request.setRequestHeader("Authorization", "Bearer " + <?php echo json_encode($_SESSION["token"]); ?>);
				},
				success: function(response){
					var MetaData = response.response_package;

					if (MetaData != ""){
						
						if (MetaData.pasien != ""){
							$("#no_rm").html(MetaData.pasien.no_rm);
							$("#tanggal_lahir").html(MetaData.pasien.tanggal_lahir);
							$("#panggilan").html(MetaData.pasien.panggilan);
							$("#nama").html(MetaData.pasien.nama);
							$("#jenkel").html(MetaData.pasien.jenkel);
						}
					}
				},
				error: function(response) {
					console.log(response);
				}
			});
		}
	}

	function loadRadiologiOrderItem(params){	//params = id radiologi_order_detail
		var dataItem;

		if (params != ""){
			$.ajax({
				async: false,
				url:__HOSTAPI__ + "/Radiologi/radiologi-order-detail-item/" + params,
				type: "GET",
				beforeSend: function(request) {
					request.setRequestHeader("Authorization", "Bearer " + <?php echo json_encode($_SESSION["token"]); ?>);
				},
				success: function(response){
					if (response.response_package != undefined){
						dataItem = response.response_package.response_data;
					}
				},
				error: function(response) {
					console.log(response);
				}
			});
		}

		return dataItem;
	}

	function loadLampiran(uid_order){
		let dataItem;

		if (uid_order != ""){
			$.ajax({
				async: false,
				url:__HOSTAPI__ + "/Radiologi/get-radiologi-lampiran/" + uid_order,
				type: "GET",
				beforeSend: function(request) {
					request.setRequestHeader("Authorization", "Bearer " + <?php echo json_encode($_SESSION["token"]); ?>);
				},
				success: function(response){
					if (response.response_package != ""){
						dataItem = response.response_package.response_data;
						let baseUrl = __HOST__ + "/document/radiologi/" + uid_order + "/";

						/*var pdfjsLib = window["pdfjs-dist/build/pdf"];
						pdfjsLib.GlobalWorkerOptions.workerSrc = __HOSTNAME__ + "/plugins/pdfjs/build/pdf.worker.js";
						var loadingTask;*/

						$(dataItem).each(function(key, item){
							loadLampiranCanvas(baseUrl + item.lampiran, item.id);
						});
					}
				},
				error: function(response) {
					console.log(response);
				}
			});
		}

		return dataItem;
	}

	function loadLampiranCanvas(doc_url, id){
		var newDocRow = document.createElement("TR");

		var newDocCellNum = document.createElement("TD");
		var newDocCellDoc = document.createElement("TD");
		$(newDocCellDoc).addClass("text-center");
		var newDocCellAct = document.createElement("TD");

		var newDocument = document.createElement("CANVAS");

		$(newDocument)
			.css({
				"width": "75%"
			})
			.attr("id", "pdfViewer_" + id);

		$(newDocCellDoc).append(newDocument);
		if (doc_url != undefined) {
			// Using DocumentInitParameters object to load binary data.
			var loadingTask = pdfjsLib.getDocument({
				url: doc_url
			});
			loadingTask.promise.then(function(pdf) {
				// Fetch the first page
				var pageNumber = 1;
				pdf.getPage(pageNumber).then(function(page) {
					var scale = 1.5;
					var viewport = page.getViewport({
						scale: scale
					});
					// Prepare canvas using PDF page dimensions
					var canvas = $(newDocument)[0];
					var context = canvas.getContext("2d");
					canvas.height = viewport.height;
					canvas.width = viewport.width;
					// Render PDF page into canvas context
					var renderContext = {
						canvasContext: context,
						viewport: viewport
					};
					var renderTask = page.render(renderContext);
					renderTask.promise.then(function() {
						//
					});
				});
			}, function(reason) {
				console.error(reason);
			});
		}

		var newDeleteDoc = document.createElement("button");
		$(newDeleteDoc)
			.addClass("btn btn-sm btn-danger delete_document_registered")
			.html("<span style=\"display: block;\"><i class=\"fa fa-trash\"></i></span>")
			.attr("type", "button")
			.data("id", "lampiran_" + id);

		$(newDocCellAct).append(newDeleteDoc);

		$(newDocRow).append(newDocCellNum);
		$(newDocRow).append(newDocCellDoc);
		$(newDocRow).append(newDocCellAct);

		$("#radiologi-lampiran-table").append(newDocRow);
		rebaseLampiran();
	}

	//fungsi untuk tanda ceklis tab lampiran
	/*function check_page_2() {
		if($("#po_document_table tbody tr").length > 0) {
			$("#status-dokumen").fadeIn();
		} else {
			$("#status-dokumen").fadeOut();
		}
	}*/

	function autoDocument(file) {
		var newDocRow = document.createElement("TR");

		var newDocCellNum = document.createElement("TD");
		var newDocCellDoc = document.createElement("TD");
		$(newDocCellDoc).addClass("text-center");
		var newDocCellAct = document.createElement("TD");

		var newDocument = document.createElement("CANVAS");
		$(newDocument).css({
			"width": "75%"
		});
		$(newDocCellDoc).append(newDocument);
		if (file.type == "application/pdf" && file != undefined) {
			var fileReader = new FileReader();
			fileReader.onload = function() {
				var pdfData = new Uint8Array(this.result);
				// Using DocumentInitParameters object to load binary data.
				var loadingTask = pdfjsLib.getDocument({
					data: pdfData
				});
				loadingTask.promise.then(function(pdf) {
					// Fetch the first page
					var pageNumber = 1;
					pdf.getPage(pageNumber).then(function(page) {
						var scale = 1.5;
						var viewport = page.getViewport({
							scale: scale
						});
						// Prepare canvas using PDF page dimensions
						var canvas = $(newDocument)[0];
						var context = canvas.getContext("2d");
						canvas.height = viewport.height;
						canvas.width = viewport.width;
						// Render PDF page into canvas context
						var renderContext = {
							canvasContext: context,
							viewport: viewport
						};
						var renderTask = page.render(renderContext);
						renderTask.promise.then(function() {
							//
						});
					});
				}, function(reason) {
					console.error(reason);
				});
			};
			fileReader.readAsArrayBuffer(file);
		}

		var newDeleteDoc = document.createElement("button");
		$(newDeleteDoc).addClass("btn btn-sm btn-danger delete_document").html("<span style=\"display: block;\"><i class=\"fa fa-trash\"></i></span>").attr("type", "button");
		$(newDocCellAct).append(newDeleteDoc);

		$(newDocRow).append(newDocCellNum);
		$(newDocRow).append(newDocCellDoc);
		$(newDocRow).append(newDocCellAct);

		$("#radiologi-lampiran-table").append(newDocRow);
		rebaseLampiran();
	}

	function rebaseLampiran() {
		$("#radiologi-lampiran-table tbody tr").each(function(e) {
			var id = (e + 1);
			$(this).attr({
				"id": "document_" + id
			});
			$(this).find("td:eq(0)").html((e + 1));
			$(this).find("td:eq(2) button").attr({
				"id": "delete_document_" + id
			});
		});
	}

	//for checking pdf file
	function checkFile(file_name) {
        let fileExtension = "";

        if (file_name.lastIndexOf(".") > 0) {
            fileExtension = file_name.substring(file_name.lastIndexOf(".") + 1, file_name.length);
        }

        if (fileExtension.toLowerCase() == "pdf") {
            return true;
        }
        else {
            return false;
        }
    }

	//fungsi untuk editor textarea
	function MyCustomUploadAdapterPlugin( editor ) {
		editor.plugins.get( "FileRepository" ).createUploadAdapter = ( loader ) => {
			var MyCust = new MyUploadAdapter( loader );
			var dataToPush = MyCust.imageList;
			hiJackImage(dataToPush);
			return MyCust;
		};
	}
</script>

<div id="form-upload-lampiran" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-large-title" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modal-large-title">Upload Lampiran</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group col-md-12">
					<canvas style="width: 100%; border: solid 1px #ccc;" id="pdfViewer"></canvas>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Kembali</button>
				<button type="button" class="btn btn-primary" id="btnSubmitLampiran">Submit</button>
			</div>
		</div>
	</div>
</div>