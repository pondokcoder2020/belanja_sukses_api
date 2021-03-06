<script src="<?php echo __HOSTNAME__; ?>/plugins/ckeditor5-build-classic/ckeditor.js"></script>
<script type="text/javascript">
	$(function() {
	    //load-kandungan-obat
		var MODE = "edit";
		var UID = __PAGES__[3];
		var selectedDariSatuanList = [];
		var invData;
		var editorKeterangan;

		/*ClassicEditor.create(document.querySelector("#txt_keterangan"), {
            extraPlugins: [ MyCustomUploadAdapterPlugin ],
            placeholder: "Keterangan Produk..."
        }).then(editor => {
            editorKeterangan = editor;
            window.editor = editor;
        })
        .catch( err => {
            //console.error( err.stack );
        });*/

		var imageResultPopulator = [];
		var selectedKategoriObat = [];

		$.ajax({
			url:__HOSTAPI__ + "/Inventori/item_detail/" + UID,
			async:false,
			beforeSend: function(request) {
				request.setRequestHeader("Authorization", "Bearer " + <?php echo json_encode($_SESSION["token"]); ?>);
			},
			type:"GET",
			success:function(response) {
                console.clear();

                invData = response.response_data;

                var populatePoint = {};

                for(var ap in invData.point) {
                    if(populatePoint["data_" + invData.point[ap].id_table] === undefined) {
                        populatePoint["data_" + invData.point[ap].id_table] = {
                            tanggal:invData.point[ap].tanggal,
                            stokis_het: 0,
                            stokis_discount_type: "",
                            stokis_discount: 0,
                            stokis_harga_jual: 0,

                            member_het: 0,
                            member_discount_type: "",
                            member_discount: 0,
                            member_harga_jual: 0,

                            member_cashback: 0,
                            member_reward: 0,
                            member_royalti: 0,
                            member_insentif: 0,

                            stokis_cashback: 0,
                            stokis_reward: 0,
                            stokis_royalti: 0,
                            stokis_insentif: 0
                        };
                    }

                    if(invData.point[ap].member_type === "M") {
                        populatePoint["data_" + invData.point[ap].id_table].member_het = (invData.point[ap].het !== undefined) ? parseFloat(invData.point[ap].het) : 0;
                        populatePoint["data_" + invData.point[ap].id_table].member_discount_type = (invData.point[ap].discount_type !== undefined) ? invData.point[ap].discount_type : "N";
                        populatePoint["data_" + invData.point[ap].id_table].member_discount = (invData.point[ap].discount !== undefined) ? parseFloat(invData.point[ap].discount) : 0;
                        populatePoint["data_" + invData.point[ap].id_table].member_harga_jual = (invData.point[ap].jual !== undefined) ? parseFloat(invData.point[ap].jual) : 0;

                        populatePoint["data_" + invData.point[ap].id_table].member_cashback = (invData.point[ap].cashback !== undefined) ? parseFloat(invData.point[ap].cashback) : 0;
                        populatePoint["data_" + invData.point[ap].id_table].member_reward = (invData.point[ap].reward !== undefined) ? parseFloat(invData.point[ap].reward) : 0;
                        populatePoint["data_" + invData.point[ap].id_table].member_royalti = (invData.point[ap].royalti !== undefined) ? parseFloat(invData.point[ap].royalti) : 0;
                        populatePoint["data_" + invData.point[ap].id_table].member_insentif = parseFloat(invData.point[ap].insentif);
                    } else {
                        populatePoint["data_" + invData.point[ap].id_table].stokis_het = (invData.point[ap].het !== undefined) ? parseFloat(invData.point[ap].het) : 0;
                        populatePoint["data_" + invData.point[ap].id_table].stokis_discount_type = (invData.point[ap].discount_type !== undefined) ? invData.point[ap].discount_type : "N";
                        populatePoint["data_" + invData.point[ap].id_table].stokis_discount = (invData.point[ap].discount !== undefined) ? parseFloat(invData.point[ap].discount) : 0;
                        populatePoint["data_" + invData.point[ap].id_table].stokis_harga_jual = (invData.point[ap].jual !== undefined) ? parseFloat(invData.point[ap].jual) : 0;

                        populatePoint["data_" + invData.point[ap].id_table].stokis_cashback = (invData.point[ap].cashback !== undefined) ? parseFloat(invData.point[ap].cashback) : 0;
                        populatePoint["data_" + invData.point[ap].id_table].stokis_reward = (invData.point[ap].reward !== undefined) ? parseFloat(invData.point[ap].reward) : 0;
                        populatePoint["data_" + invData.point[ap].id_table].stokis_royalti = (invData.point[ap].royalti !== undefined) ? parseFloat(invData.point[ap].royalti) : 0;
                        populatePoint["data_" + invData.point[ap].id_table].stokis_insentif = (invData.point[ap].insentif !== undefined) ? parseFloat(invData.point[ap].insentif) : 0;
                    }
                }

                for(var abz in populatePoint) {
                    autoHargaList(populatePoint[abz]);
                }

				$("#txt_nama").val(invData.nama);
                $("#txt_het").val(invData.het);
				$(".label_nama").html(invData.nama.toUpperCase());

				if(invData.kode_barang == undefined) {
                    $("#txt_kode").val("-");
                    $(".label_kode").html("-");
                } else {
                    $("#txt_kode").val(invData.kode_barang);
                    $(".label_kode").html(invData.kode_barang.toUpperCase());
                }

				load_kategori("#txt_kategori", invData.kategori);
				/*for(var kk = 0; kk < invData.kandungan.length; kk++) {
				    autoKandungan({
                        kandungan: invData.kandungan[kk].kandungan,
                        keterangan: invData.kandungan[kk].keterangan
                    });
                }
				autoKandungan();*/

				$(".label_kategori").html($("#txt_kategori").find("option:selected").text().toUpperCase());
				//load_manufacture("#txt_manufacture", invData.manufacture);
				load_satuan("#txt_satuan_terkecil", invData.satuan_terkecil);
				if(selectedDariSatuanList.indexOf($("#txt_satuan_terkecil").val()) < 0) {
					selectedDariSatuanList.push($("#txt_satuan_terkecil").val());
				}

				/*for(var a = 0; a < invData.konversi.length; a++) {
					autoSatuan(selectedDariSatuanList, {
						dari:invData.konversi[a].dari_satuan,
						ke:invData.konversi[a].ke_satuan,
						rasio:parseFloat(invData.konversi[a].rasio)
					});	
				}*/
				autoSatuan(selectedDariSatuanList);

				var hargaList = {};
				/*for(var b = 0; b < invData.penjamin.length; b++) {
					if(hargaList[invData.penjamin[b].penjamin] == undefined) {
						hargaList[invData.penjamin[b].penjamin] = {
							profit:invData.penjamin[b].profit,
							profit_type:invData.penjamin[b].profit_type
						};
					}
				}
				autoHarga(hargaList);*/

				/*for(var c = 0; c < invData.kategori_obat.length; c++) {
					if(selectedKategoriObat.indexOf(invData.kategori_obat[c].kategori) < 0) {
						selectedKategoriObat.push(invData.kategori_obat[c].kategori);
					}
				}
				load_kategori_obat(selectedKategoriObat);
				$(".load-kategori-obat-badge").html("");
				for(var b = 0; b < selectedKategoriObat.length; b++) {
					$(".load-kategori-obat-badge").append("<div style=\"margin:5px;\" class=\"badge badge-info\"><i class=\"fa fa-tag\"></i>&nbsp;&nbsp;" + $("#label_kategori_obat_" + selectedKategoriObat[b]).html() + "</div>");
				}*/

				autoGudang(invData.lokasi, invData.monitoring);

				/*ClassicEditor.create(document.querySelector("#txt_keterangan"), {
					extraPlugins: [ MyCustomUploadAdapterPlugin ],
					placeholder: "Keterangan Produk..."
				}).then(editor => {
					editor.setData(invData.keterangan);
					editorKeterangan = editor;
					window.editor = editor;
				})
				.catch( err => {
					//console.error( err.stack );
				});*/
                ClassicEditor.create(document.querySelector("#txt_keterangan"), {
                    extraPlugins: [ MyCustomUploadAdapterPlugin ],
                    placeholder: "Keterangan Produk..."
                }).then(editor => {
                    editorKeterangan = editor;
                    editorKeterangan.setData(invData.keterangan);
                    window.editor = editor;
                })
                .catch( err => {
                    //console.error( err.stack );
                });

                for(var pakKey in invData.paket) {
                    autoPaket({
                        barang: invData.paket[pakKey].barang,
                        qty: invData.paket[pakKey].qty
                    });
                }
			}
		});

		$(".inv-tab-status").hide();

		var nama = $("#txt_nama").val();
		var kode = $("#txt_kode").val();
		var kategori = $("#txt_kategori").val();
		//var manufacture = $("#txt_manufacture").val();
		

		$("#txt_kategori").select2();
		//$("#txt_manufacture").select2();
		$("#txt_satuan_terkecil").select2();
		

		class MyUploadAdapter {
			static loader;
		    constructor( loader ) {
		        // CKEditor 5's FileLoader instance.
		        this.loader = loader;

		        // URL where to send files.
		        this.url = __HOSTAPI__ + "/Upload";

		        this.imageList = [];
		    }

		    // Starts the upload process.
		    upload() {
		        return new Promise( ( resolve, reject ) => {
		            this._initRequest();
		            this._initListeners( resolve, reject );
		            this._sendRequest();
		        } );
		    }

		    // Aborts the upload process.
		    abort() {
		        if ( this.xhr ) {
		            this.xhr.abort();
		        }
		    }

		    // Example implementation using XMLHttpRequest.
		    _initRequest() {
		        const xhr = this.xhr = new XMLHttpRequest();

		        xhr.open( 'POST', this.url, true );
		        xhr.setRequestHeader("Authorization", 'Bearer ' + <?php echo json_encode($_SESSION["admin_ciscard"]); ?>);
		        xhr.responseType = 'json';
		    }

		    // Initializes XMLHttpRequest listeners.
		    _initListeners( resolve, reject ) {
		        const xhr = this.xhr;
		        const loader = this.loader;
		        const genericErrorText = 'Couldn\'t upload file:' + ` ${ loader.file.name }.`;

		        xhr.addEventListener( 'error', () => reject( genericErrorText ) );
		        xhr.addEventListener( 'abort', () => reject() );
		        xhr.addEventListener( 'load', () => {
		            const response = xhr.response;

		            if ( !response || response.error ) {
		                return reject( response && response.error ? response.error.message : genericErrorText );
		            }

		            // If the upload is successful, resolve the upload promise with an object containing
		            // at least the "default" URL, pointing to the image on the server.
		            resolve( {
		                default: response.url
		            } );
		        } );

		        if ( xhr.upload ) {
		            xhr.upload.addEventListener( 'progress', evt => {
		                if ( evt.lengthComputable ) {
		                    loader.uploadTotal = evt.total;
		                    loader.uploaded = evt.loaded;
		                }
		            } );
		        }
		    }


		    // Prepares the data and sends the request.
		    _sendRequest() {
		    	const toBase64 = file => new Promise((resolve, reject) => {
				    const reader = new FileReader();
				    reader.readAsDataURL(file);
				    reader.onload = () => resolve(reader.result);
				    reader.onerror = error => reject(error);
				});

		    	var Axhr = this.xhr;
				
				async function doSomething(fileTarget) {
					fileTarget.then(function(result) {
						var ImageName = result.name;
						toBase64(result).then(function(renderRes) {
							const data = new FormData();
							data.append( 'upload', renderRes);
							data.append( 'name', ImageName);
							Axhr.send( data );
						});
					});
				}

				var ImageList = this.imageList;

				this.loader.file.then(function(toAddImage) {

					ImageList.push(toAddImage.name);

				});
				
				this.imageList = ImageList;

				doSomething(this.loader.file);
		    }
		}


        autoPaket();

        function autoPaket(setter = {}) {
            var newPaketRow = document.createElement("TR");
            var newPaketID = document.createElement("TD");
            var newPaketBarang = document.createElement("TD");
            var newPaketQty = document.createElement("TD");
            var newPaketSatuan = document.createElement("TD");
            var newPaketAksi = document.createElement("TD");

            var newSelectorBarang = document.createElement("SELECT");
            var newSelectorQty = document.createElement("INPUT");
            var newSelectorDelete = document.createElement("BUTTON");

            $(newPaketBarang).append(newSelectorBarang);

            $(newSelectorBarang).select2({
                minimumInputLength: 2,
                "language": {
                    "noResults": function(){
                        return "Barang tidak ditemukan";
                    }
                },
                ajax: {
                    dataType: "json",
                    headers:{
                        "Authorization" : "Bearer " + <?php echo json_encode($_SESSION["token"]); ?>,
                        "Content-Type" : "application/json",
                    },
                    url:__HOSTAPI__ + "/Inventori/get_item_select2/" + $(".select2-search__field").val(),
                    type: "GET",
                    data: function (term) {
                        return {
                            search:term.term
                        };
                    },
                    cache: true,
                    processResults: function (response) {
                        var data = response.response_data;
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.nama.toUpperCase(),
                                    id: item.uid,
                                    satuan_terkecil: item.satuan_terkecil
                                }
                            })
                        };
                    }
                }
            }).addClass("form-control item_paket").on("select2:select", function(e) {
                var data = e.params.data;
                if(data.satuan_terkecil !== undefined && data.satuan_terkecil !== null) {
                    $(this).children("[value=\""+ data.id + "\"]").attr({
                        "satuan-caption": data.satuan_terkecil
                    });
                    $(newPaketSatuan).html(data.satuan_terkecil.nama);
                } else {
                    return false;
                }

                var id = $(this).attr("id").split("_");
                id = id[id.length - 1];

                var currentValue = $("#paket_qty_" + id).inputmask("unmaskedvalue");
                if(currentValue > 0 && $("#paket_row_" + id).hasClass("last_paket") && $("#paket_barang_" + id).val() !== null && $("#paket_barang_" + id).val() !== undefined) {
                    autoPaket();
                }
            });

            console.log(setter);

            if(setter.barang !== undefined && setter.barang !== null) {
                $(newSelectorBarang).append("<option value=\"" + setter.barang.uid + "\">" + setter.barang.nama.toUpperCase() + "</option>");
                $(newSelectorBarang).select2("data", {id: setter.barang.uid, text: setter.barang.nama.toUpperCase()});
                $(newSelectorBarang).trigger("change");

                $(newPaketSatuan).html(setter.barang.satuan_terkecil_info.nama);
            } else {
                $(newPaketSatuan).html("-");
            }

            $(newPaketQty).append(newSelectorQty);
            $(newSelectorQty).inputmask({
                alias: 'decimal', rightAlign: true, placeholder: "0,00", prefix: "", groupSeparator: ".", autoGroup: false, digitsOptional: true
            }).addClass("form-control qty_paket");

            if(setter.qty !== undefined && setter.qty !== null) {
                $(newSelectorQty).val(parseInt(setter.qty));
            }

            $(newPaketAksi).append(newSelectorDelete);
            $(newSelectorDelete).addClass("btn btn-danger btn-sm paket_delete").html("<i class=\"fa fa-ban\"></i>");



            $(newPaketRow).append(newPaketID);
            $(newPaketRow).append(newPaketBarang);
            $(newPaketRow).append(newPaketQty);
            $(newPaketRow).append(newPaketSatuan);
            $(newPaketRow).append(newPaketAksi);

            //$(newPaketRow).addClass("last_paket");
            $("#table-paket").append(newPaketRow);
            rebasePaket();
        }

        function rebasePaket(){
            $("#table-paket tbody tr").each(function(e) {
                $(this).removeClass("last_paket");
                var id = (e + 1);
                $(this).attr({
                    "id": "paket_row_" + id
                });

                $(this).find("td:eq(0)").html(id);

                $(this).find("td:eq(1) select").attr({
                    "id": "paket_barang_" + id
                });

                $(this).find("td:eq(2) input").attr({
                    "id": "paket_qty_" + id
                });

                $(this).find("td:eq(4) button").attr({
                    "id": "paket_delete_" + id
                });
            });
            $("#table-paket tbody tr:last-child").addClass("last_paket");
        }

        $("body").on("keyup", ".qty_paket", function() {
            var id = $(this).attr("id").split("_");
            id = id[id.length - 1];

            var currentValue = $(this).inputmask("unmaskedvalue");
            if(currentValue > 0 && $("#paket_row_" + id).hasClass("last_paket") && $("#paket_barang_" + id).val() !== null && $("#paket_barang_" + id).val() !== undefined) {
                autoPaket();
            }
        });

        $("body").on("click", ".paket_delete", function() {
            var id = $(this).attr("id").split("_");
            id = id[id.length - 1];
            if(!$("#paket_row_" + id).hasClass("last_paket")) {
                $("#paket_row_" + id).remove();
                rebasePaket();
            }
        });


		function MyCustomUploadAdapterPlugin( editor ) {
		    editor.plugins.get( 'FileRepository' ).createUploadAdapter = ( loader ) => {
		        var MyCust = new MyUploadAdapter( loader );
		        var dataToPush = MyCust.imageList;
		        hiJackImage( dataToPush );
		        return MyCust;
		    };
		}

		
		function hiJackImage(toHi) {
			imageResultPopulator.push(toHi);
		}

		//==========================================================CROPPER
		var targetCropper = $("#image-uploader");
		var basic = targetCropper.croppie({
			enforceBoundary:false,
			viewport: {
				width: 220,
				height: 220
			},
		});
		if(invData.url_gambar !== undefined) {
			if(invData.url_gambar[0] === undefined) {
				basic.croppie("bind", {
					zoom: 1,
					url: __HOST__ + "/assets/images/inventori/unset.png"
				});
			} else {
				basic.croppie("bind", {
					zoom: 1,
					url: __HOST__ + "/images/produk/" + UID + ".png"
				});
			}
		} else {
		    alert();
			basic.croppie("bind", {
				zoom: 1,
				url: __HOST__ + "/assets/images/inventori/unset.png"
			});
		}

		$("#upload-image").change(function() {
			readURL(this, basic);
		});

		function readURL(input, cropper) {
			var url = input.value;
			var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
			if (input.files && input.files[0]&& (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg")) {
				var reader = new FileReader();

				reader.onload = function (e) {
					cropper.croppie('bind', {
						url: e.target.result
					});
					//$('#imageLoader').attr('src', e.target.result);
				}
				reader.readAsDataURL(input.files[0]);
			}
			else{
				//$('#img').attr('src', '/assets/no_preview.png');
			}
		}

		function load_satuan(target, selected = "", selectedData = []) {
			var satuanData;
			$.ajax({
				url:__HOSTAPI__ + "/Inventori/satuan",
				async:false,
				beforeSend: function(request) {
					request.setRequestHeader("Authorization", "Bearer " + <?php echo json_encode($_SESSION["token"]); ?>);
				},
				type:"GET",
				success:function(response) {
					satuanData = response.response_data;
					$(target).find("option").remove();
					for(var a = 0; a < satuanData.length; a++) {
						if(selectedData.indexOf(satuanData[a].uid) < 0) {
							$(target).append("<option " + ((satuanData[a].uid == selected) ? "selected=\"selected\"" : "") + " value=\"" + satuanData[a].uid + "\">" + satuanData[a].nama + "</option>");
						} else {
							$(target).append("<option " + ((satuanData[a].uid == selected) ? "selected=\"selected\"" : "") + " value=\"" + satuanData[a].uid + "\">" + satuanData[a].nama + "</option>");
						}
					}
				},
				error: function(response) {
					console.log(response);
				}
			});
			return satuanData;
		}

		function load_kategori_obat(checkedData = []) {
			var kategoriObatData;
			$.ajax({
				url:__HOSTAPI__ + "/Inventori/kategori_obat",
				async:false,
				beforeSend: function(request) {
					request.setRequestHeader("Authorization", "Bearer " + <?php echo json_encode($_SESSION["token"]); ?>);
				},
				type:"GET",
				success:function(response) {
					kategoriObatData = response.response_data;
					render_kategori_obat(kategoriObatData, checkedData);
				},
				error: function(response) {
					console.log(response);
				}
			});
			return kategoriObatData;
		}

		function load_kategori(target, selected = "") {
			var kategoriData;
			$.ajax({
				url:__HOSTAPI__ + "/Inventori/kategori",
				async:false,
				beforeSend: function(request) {
					request.setRequestHeader("Authorization", "Bearer " + <?php echo json_encode($_SESSION["token"]); ?>);
				},
				type:"GET",
				success:function(response) {
					kategoriData = response.response_data;
					$(target).find("option").remove();
					for(var a = 0; a < kategoriData.length; a++) {
						$(target).append("<option " + ((kategoriData[a].uid == selected) ? "selected=\"selected\"" : "") +  " value=\"" + kategoriData[a].uid + "\">" + kategoriData[a].nama + "</option>");
					}
					$(".label_kategori").html($(target).find("option:selected").text().toUpperCase());
				},
				error: function(response) {
					console.log(response);
				}
			});
			return kategoriData;
		}

		function load_manufacture(target, selected = "") {
			var manufactureData;
			$.ajax({
				url:__HOSTAPI__ + "/Inventori/manufacture",
				async:false,
				beforeSend: function(request) {
					request.setRequestHeader("Authorization", "Bearer " + <?php echo json_encode($_SESSION["token"]); ?>);
				},
				type:"GET",
				success:function(response) {
					manufactureData = response.response_data;
					$(target).find("option").remove();
					for(var a = 0; a < manufactureData.length; a++) {
						$(target).append("<option " + ((manufactureData[a].uid == selected) ? "selected=\"selected\"" : "") + " value=\"" + manufactureData[a].uid + "\">" + manufactureData[a].nama + "</option>");
					}
					$(".label_manufacture").html($(target).find("option:selected").text().toUpperCase());
				},
				error: function(response) {
					console.log(response);
				}
			});
			return manufactureData;
		}

		function render_kategori_obat(data, checked = []) {
			for(var key in data) {
				var newList =
				"<li style=\"margin-bottom: 10px;\">" +
					"<div class=\"custom-control custom-checkbox-toggle custom-control-inline mr-1\">" +
						"<input type=\"checkbox\" " + ((checked.indexOf(data[key].uid) > -1) ? "checked=\"checked\"" : "") + " id=\"kategori_obat_" + data[key].uid + "\" class=\"custom-control-input kategori_obat_selection\">" +
						"<label class=\"custom-control-label\" for=\"kategori_obat_" + data[key].uid + "\">Yes</label>" +
					"</div>" +
					"<label id=\"label_kategori_obat_" + data[key].uid + "\" for=\"kategori_obat_" + data[key].uid + "\" class=\"mb-0\">" + data[key].nama + "</label>" +
				"</li>";
				$("#load-kategori-obat").append(newList);
			}
		}

		function autoHargaList(setter = {
		    tanggal:"",
            stokis_het: 0,
            stokis_discount_type: "",
            stokis_discount: 0,
            stokis_harga_jual: 0,

            stokis_cashback: 0,
            stokis_royalti: 0,
            stokis_reward: 0,
            stokis_insentif: 0,

            member_het: 0,
            member_discount_type: "",
            member_discount: 0,
            member_harga_jual: 0,

            member_cashback: 0,
            member_royalti: 0,
            member_reward: 0,
            member_insentif: 0,
        }) {
		    var Stokisrow = document.createElement("TR");
            var Memberrow = document.createElement("TR");

		    var CellID = document.createElement("TD");
		    var CellTanggal = document.createElement("TD");
            var CellStokis = document.createElement("TD");
            var CellMember = document.createElement("TD");


            var CellStokisHET = document.createElement("TD");
		    var CellStokisDiskon = document.createElement("TD");
		    var CellStokisJual = document.createElement("TD");
            var CellStokisPoint = document.createElement("TD");

            var CellMemberHET = document.createElement("TD");
            var CellMemberDiskon = document.createElement("TD");
            var CellMemberJual = document.createElement("TD");
            var CellMemberPoint = document.createElement("TD");

		    var CellAksi = document.createElement("TD");

		    var Stokishet = document.createElement("INPUT");
            var Stokisdiscount = document.createElement("INPUT");
            var StokisdiscountType = document.createElement("SELECT");
            var StokishargaJual = document.createElement("INPUT");

            var StokisCashback = document.createElement("INPUT");
            var StokisReward = document.createElement("INPUT");
            var StokisRoyalti = document.createElement("INPUT");
            var StokisInsentif = document.createElement("INPUT");

            var Memberhet = document.createElement("INPUT");
            var Memberdiscount = document.createElement("INPUT");
            var MemberdiscountType = document.createElement("SELECT");
            var MemberhargaJual = document.createElement("INPUT");

            var MemberCashback = document.createElement("INPUT");
            var MemberReward = document.createElement("INPUT");
            var MemberRoyalti = document.createElement("INPUT");
            var MemberInsentif = document.createElement("INPUT");


            var btnDelete = document.createElement("BUTTON");

            $(CellStokisHET).append(Stokishet);
            $(CellStokisDiskon).append("<div class=\"row\"><div class=\"col-md-4\"></div><div class=\"col-md-8\"></div></div>");
            $(CellStokisDiskon).find("div.col-md-4").append(StokisdiscountType);
            $(CellStokisDiskon).find("div.col-md-8").append(Stokisdiscount);
            $(CellStokisJual).append(StokishargaJual);

            $(CellMemberHET).append(Memberhet);
            $(CellMemberDiskon).append("<div class=\"row\"><div class=\"col-md-4\"></div><div class=\"col-md-8\"></div></div>");
            $(CellMemberDiskon).find("div.col-md-4").append(MemberdiscountType);
            $(CellMemberDiskon).find("div.col-md-8").append(Memberdiscount);
            $(CellMemberJual).append(MemberhargaJual);

            $(CellAksi).append(btnDelete);

            $(Stokishet).addClass("form-control stokis_het").inputmask({
                alias: 'currency', rightAlign: true, placeholder: "0,00", prefix: "", autoGroup: false, digitsOptional: true
            }).val(setter.stokis_het);
            $(StokisdiscountType).addClass("form-control stokis_discount_type").append("<option value=\"N\">-</option><option value=\"P\">%</option><option value=\"A\">Amt</option>");
            $(StokisdiscountType).find("option[value=\"" + setter.stokis_discount_type + "\"]").prop("selected", true);
            $(StokisdiscountType).select2();
            $(Stokisdiscount).addClass("form-control stokis_discount").inputmask({
                alias: 'currency', rightAlign: true, placeholder: "0,00", prefix: "", autoGroup: false, digitsOptional: true
            }).val(setter.stokis_discount);
            $(StokishargaJual).addClass("form-control stokis_harga").inputmask({
                alias: 'currency', rightAlign: true, placeholder: "0,00", prefix: "", autoGroup: false, digitsOptional: true
            }).attr({
                "disabled": "disabled"
            }).val(setter.stokis_harga_jual);


            $(Memberhet).addClass("form-control member_het").inputmask({
                alias: 'currency', rightAlign: true, placeholder: "0,00", prefix: "", autoGroup: false, digitsOptional: true
            }).val(setter.member_het);
            $(MemberdiscountType).addClass("form-control member_discount_type").append("<option value=\"N\">-</option><option value=\"P\">%</option><option value=\"A\">Amt</option>");
            $(MemberdiscountType).find("option[value=\"" + setter.member_discount_type + "\"]").prop("selected", true);
            $(MemberdiscountType).select2();
            $(Memberdiscount).addClass("form-control member_discount").inputmask({
                alias: 'currency', rightAlign: true, placeholder: "0,00", prefix: "", autoGroup: false, digitsOptional: true
            }).val(setter.member_discount);
            $(MemberhargaJual).addClass("form-control member_jual").inputmask({
                alias: 'currency', rightAlign: true, placeholder: "0,00", prefix: "", autoGroup: false, digitsOptional: true
            }).attr({
                "disabled": "disabled"
            }).val(setter.member_harga_jual);

            $(btnDelete).addClass("btn btn-sm btn-danger").html("<i class=\"fa fa-trash-alt\"></i>");

            $(Stokisrow).append(CellID);
            $(Stokisrow).append(CellTanggal);
            $(Stokisrow).append(CellStokis);
            $(Stokisrow).append(CellStokisHET);
            $(Stokisrow).append(CellStokisDiskon);
            $(Stokisrow).append(CellStokisJual);
            $(Stokisrow).append(CellStokisPoint);

            $(CellStokisPoint).append("<div class=\"row\"><div class=\"col-md-6\"></div><div class=\"col-md-6\"></div><div class=\"col-md-6\"></div><div class=\"col-md-6\"></div></div>");
            $(CellStokisPoint).find(".col-md-6:eq(0)").append("Cashback :").append(StokisCashback).append("<br />");
            $(CellStokisPoint).find(".col-md-6:eq(1)").append("Reward :").append(StokisReward).append("<br />");
            $(CellStokisPoint).find(".col-md-6:eq(2)").append("Royalti :").append(StokisRoyalti).append("<br />");
            $(CellStokisPoint).find(".col-md-6:eq(3)").append("Insentif :").append(StokisInsentif).append("<br />");

            $(StokisCashback).addClass("form-control");
            $(StokisReward).addClass("form-control");
            $(StokisRoyalti).addClass("form-control");
            $(StokisInsentif).addClass("form-control");

            $(Memberrow).append(CellMember);
            $(Memberrow).append(CellMemberHET);
            $(Memberrow).append(CellMemberDiskon);
            $(Memberrow).append(CellMemberJual);
            $(Memberrow).append(CellMemberPoint);

            $(CellMemberPoint).append("<div class=\"row\"><div class=\"col-md-6\"></div><div class=\"col-md-6\"></div><div class=\"col-md-6\"></div><div class=\"col-md-6\"></div></div>");
            $(CellMemberPoint).find(".col-md-6:eq(0)").append("Cashback :").append(MemberCashback).append("<br />");
            $(CellMemberPoint).find(".col-md-6:eq(1)").append("Reward :").append(MemberReward).append("<br />");
            $(CellMemberPoint).find(".col-md-6:eq(2)").append("Royalti :").append(MemberRoyalti).append("<br />");
            $(CellMemberPoint).find(".col-md-6:eq(3)").append("Insentif :").append(MemberInsentif).append("<br />");

            $(MemberCashback).addClass("form-control");
            $(MemberReward).addClass("form-control");
            $(MemberRoyalti).addClass("form-control");
            $(MemberInsentif).addClass("form-control");

            $(StokisCashback).addClass("form-control stokis_cashback").inputmask({
                alias: 'currency', rightAlign: true, placeholder: "0,00", prefix: "", autoGroup: false, digitsOptional: true
            }).val((setter.stokis_cashback === undefined || isNaN(setter.stokis_cashback)) ? 0 : setter.stokis_cashback);

            $(StokisReward).addClass("form-control stokis_reward").inputmask({
                alias: 'currency', rightAlign: true, placeholder: "0,00", prefix: "", autoGroup: false, digitsOptional: true
            }).val((setter.stokis_reward === undefined) ? 0 : setter.stokis_reward);

            $(StokisRoyalti).addClass("form-control stokis_royalti").inputmask({
                alias: 'currency', rightAlign: true, placeholder: "0,00", prefix: "", autoGroup: false, digitsOptional: true
            }).val((setter.stokis_royalti === undefined) ? 0 : setter.stokis_royalti);

            $(StokisInsentif).addClass("form-control stokis_insentif").inputmask({
                alias: 'currency', rightAlign: true, placeholder: "0,00", prefix: "", autoGroup: false, digitsOptional: true
            }).val((setter.stokis_insentif === undefined) ? 0 : setter.stokis_insentif);




            $(MemberCashback).addClass("form-control member_cashback").inputmask({
                alias: 'currency', rightAlign: true, placeholder: "0,00", prefix: "", autoGroup: false, digitsOptional: true
            }).val((setter.member_cashback === undefined) ? 0 : setter.member_cashback);

            $(MemberReward).addClass("form-control member_reward").inputmask({
                alias: 'currency', rightAlign: true, placeholder: "0,00", prefix: "", autoGroup: false, digitsOptional: true
            }).val((setter.member_reward === undefined) ? 0 : setter.member_reward);

            $(MemberRoyalti).addClass("form-control member_royali").inputmask({
                alias: 'currency', rightAlign: true, placeholder: "0,00", prefix: "", autoGroup: false, digitsOptional: true
            }).val((setter.member_royalti === undefined) ? 0 : setter.member_royalti);

            $(MemberInsentif).addClass("form-control member_insentif").inputmask({
                alias: 'currency', rightAlign: true, placeholder: "0,00", prefix: "", autoGroup: false, digitsOptional: true
            }).val((setter.member_insentif === undefined) ? 0 : setter.member_insentif);

            $(CellStokis).html("Stokis");
            $(CellMember).html("Member");

            $(Stokisrow).append(CellAksi);

            $(CellID).attr({
                "rowspan": 2
            });

            $(CellTanggal).attr({
                "rowspan": 2
            });

            $(CellAksi).attr({
                "rowspan": 2
            });

            $("#table-harga tbody").append(Stokisrow).append(Memberrow);
            $("#table-harga tbody tr").removeClass("last_harga_master");

            $(CellTanggal).html("<i class=\"wrap_content\">" + ((setter.tanggal === "") ? <?php echo json_encode(date("d F y")); ?> : setter.tanggal) + "</i>");

            $(Stokisrow).addClass("last_harga_master");

            rebaseHarga();
        }

        function rebaseHarga() {


		    $("#table-harga tbody tr").each(function (e) {
                var id = (e + 1);

                var groupID = (((e % 2) === 0) ? (id + 1) : (id + 0)) / 2;

                $(this).attr({
                    "id": "harga_inv_" + groupID
                });

                $(this).find("td:eq(0)[rowspan=\"2\"]").html(groupID);

                if(e % 2 === 0) {
                    $(this).find("td:eq(3) input").attr({
                        "id": "stokis_het_" + groupID
                    });

                    $(this).find("td:eq(4) select").attr({
                        "id": "stokis_discount_type_" + groupID
                    });

                    $(this).find("td:eq(4) input").attr({
                        "id": "stokis_discount_" + groupID
                    });

                    $(this).find("td:eq(5) input").attr({
                        "id": "stokis_jual_" + groupID
                    });

                    $(this).find("td:eq(6) .col-md-6:eq(0) input").attr({
                        "id": "stokis_cashback_" + groupID
                    });

                    $(this).find("td:eq(6) .col-md-6:eq(1) input").attr({
                        "id": "stokis_reward_" + groupID
                    });

                    $(this).find("td:eq(6) .col-md-6:eq(2) input").attr({
                        "id": "stokis_royalti_" + groupID
                    });

                    $(this).find("td:eq(6) .col-md-6:eq(3) input").attr({
                        "id": "stokis_insentif_" + groupID
                    });
                } else {
                    $(this).find("td:eq(1) input").attr({
                        "id": "member_het_" + groupID
                    });

                    $(this).find("td:eq(2) select").attr({
                        "id": "member_discount_type_" + groupID
                    });

                    $(this).find("td:eq(2) input").attr({
                        "id": "member_discount_" + groupID
                    });

                    $(this).find("td:eq(3) input").attr({
                        "id": "member_jual_" + groupID
                    });

                    $(this).find("td:eq(4) .col-md-6:eq(0) input").attr({
                        "id": "member_cashback_" + groupID
                    });

                    $(this).find("td:eq(4) .col-md-6:eq(1) input").attr({
                        "id": "member_reward_" + groupID
                    });

                    $(this).find("td:eq(4) .col-md-6:eq(2) input").attr({
                        "id": "member_royalti_" + groupID
                    });

                    $(this).find("td:eq(4) .col-md-6:eq(3) input").attr({
                        "id": "member_insentif_" + groupID
                    });
                }

                $(this).find("td:eq(6)[rowspan=\"2\"] button").attr({
                    "id": "delete_harga_" + groupID
                });

                if($(this).hasClass("last_harga_master")) {
                    $(this).find("td:eq(6)[rowspan=\"2\"] button").fadeOut();
                } else {
                    $(this).find("td:eq(6)[rowspan=\"2\"] button").show();
                }
            });
        }

        function checkListHarga(id, type, UID) {
		    var stokis_het = parseFloat($("#stokis_het_" + id).inputmask("unmaskedvalue"));
		    var stokis_discount_type = $("#stokis_discount_type_" + id).val();
            var stokis_discount = parseFloat($("#stokis_discount_" + id).inputmask("unmaskedvalue"));
            //var stokis_jual = parseFloat($("#stokis_jual_" + id).inputmask("unmaskedvalue"));

            var stokis_cashback = parseFloat($("#stokis_cashback_" + id).inputmask("unmaskedvalue"));
            var stokis_reward = parseFloat($("#stokis_reward_" + id).inputmask("unmaskedvalue"));
            var stokis_royalti = parseFloat($("#stokis_royalti_" + id).inputmask("unmaskedvalue"));
            var stokis_insentif = parseFloat($("#stokis_insentif_" + id).inputmask("unmaskedvalue"));

            var member_het = parseFloat($("#member_het_" + id).inputmask("unmaskedvalue"));
            var member_discount_type = $("#member_discount_type_" + id).val();
            var member_discount = parseFloat($("#member_discount_" + id).inputmask("unmaskedvalue"));
            //var member_jual = parseFloat($("#member_jual_" + id).inputmask("unmaskedvalue"));

            var member_cashback = parseFloat($("#member_cashback_" + id).inputmask("unmaskedvalue"));
            var member_reward = parseFloat($("#member_reward_" + id).inputmask("unmaskedvalue"));
            var member_royalti = parseFloat($("#member_royalti_" + id).inputmask("unmaskedvalue"));
            var member_insentif = parseFloat($("#member_insentif_" + id).inputmask("unmaskedvalue"));

            var stokis_jual = 0;
            var member_jual = 0;

            if(stokis_discount_type === "N") {
                stokis_jual = stokis_het;
            } else if(stokis_discount_type === "P") {
                stokis_jual = stokis_het - ((stokis_discount / 100) * stokis_het);
            } else {
                stokis_jual = stokis_het - stokis_discount;
            }

            if(member_discount_type === "N") {
                member_jual = member_het;
            } else if(member_discount_type === "P") {
                member_jual = member_het - ((member_discount / 100) * member_het);
            } else {
                member_jual = member_het - member_discount;
            }

            $("#stokis_jual_" + id).val(parseFloat(stokis_jual));
            $("#member_jual_" + id).val(parseFloat(member_jual));

            if(
                $("#harga_inv_" + id).hasClass("last_harga_master") &&
                stokis_het > 0 &&
                member_het > 0 &&
                stokis_jual > 0 &&
                member_jual > 0
            ) {
                $.ajax({
                    url: __HOSTAPI__ + "/Inventori",
                    async: false,
                    beforeSend: function (request) {
                        request.setRequestHeader("Authorization", "Bearer " + <?php echo json_encode($_SESSION["token"]); ?>);
                    },
                    type: "POST",
                    data: {
                        request: "update_harga",
                        id: id,
                        produk: UID,
                        stokis_het: (isNaN(stokis_het)) ? 0 : stokis_het,
                        member_het: (isNaN(member_het)) ? 0 : member_het,
                        stokis_jual: stokis_jual3

                        ,
                        member_jual: member_jual,
                        stokis_discount_type: stokis_discount_type,
                        stokis_discount: stokis_discount,
                        member_discount_type: member_discount_type,
                        member_discount: member_discount,

                        member_cashback: member_cashback,
                        member_reward: member_reward,
                        member_royalti: member_royalti,
                        member_insentif: member_insentif,

                        stokis_cashback: stokis_cashback,
                        stokis_reward: stokis_reward,
                        stokis_royalti: stokis_royalti,
                        stokis_insentif: stokis_insentif
                    },
                    success: function (response) {
                        autoHargaList();
                    },
                    error: function (response) {
                        //
                    }
                });
            } else {
                $.ajax({
                    url: __HOSTAPI__ + "/Inventori",
                        async: false,
                        beforeSend: function (request) {
                        request.setRequestHeader("Authorization", "Bearer " + <?php echo json_encode($_SESSION["token"]); ?>);
                    },
                    type: "POST",
                        data: {
                        request: "update_harga",
                            id: id,
                            produk: UID,
                            stokis_het: stokis_het,
                            member_het: member_het,
                            stokis_jual: stokis_jual,
                            member_jual: member_jual,
                            stokis_discount_type: stokis_discount_type,
                            stokis_discount: stokis_discount,
                            member_discount_type: member_discount_type,
                            member_discount: member_discount,

                            member_cashback: member_cashback,
                            member_reward: member_reward,
                            member_royalti: member_royalti,
                            member_insentif: member_insentif,

                            stokis_cashback: stokis_cashback,
                            stokis_reward: stokis_reward,
                            stokis_royalti: stokis_royalti,
                            stokis_insentif: stokis_insentif
                    },
                    success: function (response) {
                        //
                    },
                    error: function (response) {
                        //
                    }
                });
            }
        }

        $("body").on("keyup", ".stokis_het", function() {
            var id = $(this).attr("id");
            id = id[id.length - 1];

            checkListHarga(id, "stokis", UID);
        });

        $("body").on("keyup", ".member_het", function() {
            var id = $(this).attr("id");
            id = id[id.length - 1];

            checkListHarga(id, "member", UID);
        });

        $("body").on("keyup", ".stokis_discount", function() {
            var id = $(this).attr("id");
            id = id[id.length - 1];

            checkListHarga(id, "stokis", UID);
        });

        $("body").on("keyup", ".member_discount", function() {
            var id = $(this).attr("id");
            id = id[id.length - 1];

            checkListHarga(id, "member", UID);
        });

        $("body").on("change",".stokis_discount_type", function() {
            var id = $(this).attr("id");
            id = id[id.length - 1];

            checkListHarga(id, "stokis", UID);
        });

        $("body").on("change", ".member_discount_type", function() {
            var id = $(this).attr("id");
            id = id[id.length - 1];

            checkListHarga(id, "member", UID);
        });

        $("body").on("keyup", ".member_cashback", function() {
            var id = $(this).attr("id");
            id = id[id.length - 1];

            checkListHarga(id, "member", UID);
        });

        $("body").on("keyup", ".member_reward", function() {
            var id = $(this).attr("id");
            id = id[id.length - 1];

            checkListHarga(id, "member", UID);
        });

        $("body").on("keyup", ".member_royalti", function() {
            var id = $(this).attr("id");
            id = id[id.length - 1];

            checkListHarga(id, "member", UID);
        });

        $("body").on("keyup", ".stokis_cashback", function() {
            var id = $(this).attr("id");
            id = id[id.length - 1];

            checkListHarga(id, "member", UID);
        });

        $("body").on("keyup", ".stokis_reward", function() {
            var id = $(this).attr("id");
            id = id[id.length - 1];

            checkListHarga(id, "member", UID);
        });

        $("body").on("keyup", ".stokis_royalti", function() {
            var id = $(this).attr("id");
            id = id[id.length - 1];

            checkListHarga(id, "member", UID);
        });

        $("body").on("keyup", ".stokis_insentif", function() {
            var id = $(this).attr("id");
            id = id[id.length - 1];

            checkListHarga(id, "member", UID);
        });

        autoHargaList();

		function autoSatuan(selectedDariSatuanList, selected = {}) {
			$("#table-konversi-satuan tbody tr").removeClass("last-satuan");
			var newRowSatuan = document.createElement("TR");
			var newCellSatuanID = document.createElement("TD");
			var newCellSatuanDari = document.createElement("TD");
			var newCellSatuanKe = document.createElement("TD");
			var newCellSatuanRasio = document.createElement("TD");
			var newCellSatuanAksi = document.createElement("TD");

			var newSatuanDari = document.createElement("SELECT");
			$(newCellSatuanDari).append(newSatuanDari);
			load_satuan(newSatuanDari, selected.dari, selectedDariSatuanList);
			$(newSatuanDari).select2().addClass("satuan_dari");
			if(selectedDariSatuanList.indexOf($(newSatuanDari).val()) < 0) {
				selectedDariSatuanList.push($(newSatuanDari).val());
			}

			var newSatuanKe = document.createElement("SELECT");
			$(newCellSatuanKe).append(newSatuanKe);
			load_satuan(newSatuanKe, $("#txt_satuan_terkecil").val());
			$(newSatuanKe).select2().attr("disabled", "disabled").addClass("satuan_ke");

			var newSatuanRasio = document.createElement("INPUT");
			$(newCellSatuanRasio).append(newSatuanRasio);
			if(selected.rasio != undefined) {
				$(newSatuanRasio).val(selected.rasio);
			}
			$(newSatuanRasio).addClass("form-control").inputmask({
				alias: 'decimal',
				rightAlign: true,
				placeholder: "0.00",
				prefix: "",
				autoGroup: false,
				digitsOptional: true
			}).addClass("form-control satuan_rasio");

			var newSatuanDelete = document.createElement("BUTTON");
			$(newCellSatuanAksi).append(newSatuanDelete);
			$(newSatuanDelete).addClass("btn btn-sm btn-danger satuan_delete").html("<i class=\"fa fa-ban\"></i>");

			$(newRowSatuan).append(newCellSatuanID);
			$(newRowSatuan).append(newCellSatuanDari);
			$(newRowSatuan).append(newCellSatuanKe);
			$(newRowSatuan).append(newCellSatuanRasio);
			$(newRowSatuan).append(newCellSatuanAksi);
			$(newRowSatuan).addClass("last-satuan");
			if($(newSatuanDari).find("option").length > 0) {
				$("#table-konversi-satuan tbody").append(newRowSatuan);	
			}
			rebaseSatuan(selectedDariSatuanList);
		}

		function rebaseSatuan(selectedDariSatuanList) {
			$("#table-konversi-satuan tbody tr").each(function(e){
				var id = (e + 1);

				$(this).attr("id", "row_satuan_" + id);
				$(this).find("td:eq(0)").html(id);
				
				$(this).find("td:eq(1) select").attr("id", "satuan_dari_" + id);
				$(this).find("td:eq(2) select").attr("id", "satuan_ke_" + id);

				$(this).find("td:eq(3) input").attr("id", "satuan_rasio_" + id);
				checkSatuan(id, selectedDariSatuanList);
				$(this).find("td:eq(4) button").attr("id", "satuan_delete_" + id);
			});
		}

		function checkSatuan(id, selectedDari = []) {
			if($("#satuan_dari_" + id).val() == $("#satuan_ke_" + id).val()) {
				$("#satuan_rasio_" + id).attr("disabled", "disabled");
			} else {
				/*if(selectedDari.indexOf($("#satuan_dari_" + id).val()) < 0) {
					$("#satuan_rasio_" + id).removeAttr("disabled");
				} else {
					$("#satuan_rasio_" + id).attr("disabled", "disabled");
				}*/
				$("#satuan_rasio_" + id).removeAttr("disabled");
			}
		}

		function autoHarga(setData = {}) {
		    var penjaminData;
			$("#table-penjamin tbody tr").remove();
			$.ajax({
				url:__HOSTAPI__ + "/Penjamin/penjamin",
				async:false,
				beforeSend: function(request) {
					request.setRequestHeader("Authorization", "Bearer " + <?php echo json_encode($_SESSION["token"]); ?>);
				},
				type:"GET",
				success:function(response) {
					penjaminData = response.response_data;
					for(var a = 0; a < penjaminData.length; a++) {
						var newHargaRow = document.createElement("TR");
						$(newHargaRow).attr({
							"id": "penjamin_harga_" + penjaminData[a].uid
						});
						var newCellPenjaminID = document.createElement("TD");
						var newCellPenjaminName = document.createElement("TD");
						var newCellPenjaminMarginType = document.createElement("TD");
						var newCellPenjaminHarga = document.createElement("TD");


						$(newCellPenjaminID).html(a + 1);
						$(newCellPenjaminName).html(penjaminData[a].nama);

						var newPenjaminMarginType = document.createElement("SELECT");
						$(newCellPenjaminMarginType).append(newPenjaminMarginType);
						//setData[penjaminData[a].uid].profit
						var profitTypeList = [
							{"P":"Percent"},
							{"A":"Amount"}
						];
						for(var profKey in profitTypeList) {
							for(var profValue in profitTypeList[profKey]) {
								if(setData[penjaminData[a].uid] !== undefined) {
									$(newPenjaminMarginType).append("<option " + ((profValue == setData[penjaminData[a].uid].profit_type) ? "selected=\"selected\"" : "") + " value=\"" + profValue + "\">" + profitTypeList[profKey][profValue] + "</option>");
								} else {
									$(newPenjaminMarginType).append("<option value=\"" + profValue + "\">" + profitTypeList[profKey][profValue] + "</option>");
								}
							}
						}
						$(newPenjaminMarginType).addClass("form-control").select2();

						var newPenjaminHarga = document.createElement("INPUT");
						$(newCellPenjaminHarga).append(newPenjaminHarga);
						$(newPenjaminHarga).addClass("form-control").inputmask({
							alias: 'currency',
							rightAlign: true,
							placeholder: "0.00",
							prefix: "",
							autoGroup: false,
							digitsOptional: true
						}).val((setData[penjaminData[a].uid] == undefined) ? 0 : setData[penjaminData[a].uid].profit);
						$(newHargaRow).append(newCellPenjaminID);
						$(newHargaRow).append(newCellPenjaminName);
						$(newHargaRow).append(newCellPenjaminName);
						$(newHargaRow).append(newCellPenjaminMarginType);
						$(newHargaRow).append(newCellPenjaminHarga);

						$("#table-penjamin tbody").append(newHargaRow);
					}
				},
				error: function(response) {
					console.log(response);
				}
			});
			return penjaminData;
		}


		function autoKandungan(setter = {
		    kandungan: "",
            keterangan: ""
        }) {
            $("#load-kandungan-obat tbody tr").removeClass("last_kandungan");

            var newKandunganRow = document.createElement("TR");

            var newKandunganID = document.createElement("TD");
            var newKandunganName = document.createElement("TD");
            var newKandunganKeterangan = document.createElement("TD");
            var newKandunganAksi = document.createElement("TD");

            $(newKandunganRow).append(newKandunganID);
            $(newKandunganRow).append(newKandunganName);
            var newKandunganNameInput = document.createElement("input");
            $(newKandunganNameInput).addClass("form-control kandungan-check").attr("placeholder", "Kandungan Obat");
            $(newKandunganName).append(newKandunganNameInput);
            $(newKandunganNameInput).val(setter.kandungan);

            $(newKandunganRow).append(newKandunganKeterangan);
            var newKandunganKeteranganInput = document.createElement("input");
            $(newKandunganKeteranganInput).addClass("form-control kandungan-check").attr("placeholder", "Keterangan");
            $(newKandunganKeterangan).append(newKandunganKeteranganInput);
            $(newKandunganKeteranganInput).val(setter.keterangan);

            $(newKandunganRow).append(newKandunganAksi);
            var newKandunganDelete = document.createElement("button");
            $(newKandunganDelete).addClass("btn btn-sm btn-danger btn-delete-kandungan").html("<i class=\"fa fa-trash\"></i>");
            $(newKandunganAksi).append(newKandunganDelete);


            $(newKandunganRow).addClass("last_kandungan");

            $("#load-kandungan-obat tbody").append(newKandunganRow);

            rebaseKandungan();
        }

        $("body").on("keyup", ".kandungan-check", function() {
            var id = $(this).attr("id").split("_");
            id = id[id.length - 1];

            if(
                $("#nama_kandungan_" + id).val() !== "" &&
                $("#row_kandungan_" + id).hasClass("last_kandungan")
            ) {
                autoKandungan();
            }
        });

		$("body").on("click", ".btn-delete-kandungan", function() {
            var id = $(this).attr("id").split("_");
            id = id[id.length - 1];

            if(!$("#row_kandungan_" + id).hasClass("last_kandungan")) {
                $("#row_kandungan_" + id).remove();
                rebaseKandungan();
            }
        });

        function rebaseKandungan() {
            $("#load-kandungan-obat tbody tr").each(function(e) {
                var currentID = (e + 1);
                $(this).attr("id", "row_kandungan_" + currentID);
                $(this).find("td:eq(0)").html(currentID);
                $(this).find("td:eq(1) input").attr("id", "nama_kandungan_" + currentID);
                $(this).find("td:eq(2) input").attr("id", "keterangan_kandungan_" + currentID);
                $(this).find("td:eq(3) button").attr("id", "delete_kandungan_" + currentID);
            });
        }



		function autoGudang(lokasi, monitoring) {
			var gudangData;
			$("#table-lokasi-gudang tbody tr").remove();
			$("#table-monitoring tbody tr").remove();
			$.ajax({
				url:__HOSTAPI__ + "/Inventori/gudang",
				async:false,
				beforeSend: function(request) {
					request.setRequestHeader("Authorization", "Bearer " + <?php echo json_encode($_SESSION["token"]); ?>);
				},
				type:"GET",
				success:function(response) {
					gudangData = response.response_data;
					/*for(var a = 0; a < gudangData.length; a++) {
						var newGudangRow = document.createElement("TR");
						$(newGudangRow).attr({
							"id": "gudang_" + gudangData[a].uid
						});
						var newCellGudangID = document.createElement("TD");
						var newCellGudangName = document.createElement("TD");
						var newCellGudangLokasi= document.createElement("TD");

						$(newCellGudangID).html((a + 1));
						$(newCellGudangName).html(gudangData[a].nama);

						var newGudangLokasi = document.createElement("INPUT");
						$(newCellGudangLokasi).append(newGudangLokasi);
						$(newGudangLokasi).addClass("form-control").val("");
						for(var b = 0; b < lokasi.length; b++) {
							if(gudangData[a].uid == lokasi[b].gudang) {
								$(newGudangLokasi).val(lokasi[b].rak);
							}	
						}
						

						$(newGudangRow).append(newCellGudangID);
						$(newGudangRow).append(newCellGudangName);
						$(newGudangRow).append(newCellGudangLokasi);

						$("#table-lokasi-gudang tbody").append(newGudangRow);

						//==============================MONITORING
						var newMonitoringRow = document.createElement("TR");
						$(newMonitoringRow).attr({
							"id": "monitoring_row_" + gudangData[a].uid
						});

						var newMonitoringCellGudangID = document.createElement("TD");
						var newMonitoringCellGudangName = document.createElement("TD");
						var newMonitoringCellGudangMinimum = document.createElement("TD");
						var newMonitoringCellGudangMaximum = document.createElement("TD");
						var newMonitoringCellGudangSatuan = document.createElement("TD");

						$(newMonitoringCellGudangID).html((a + 1));
						$(newMonitoringCellGudangName).html(gudangData[a].nama);

						var nilaiMin = 0;
                        var nilaiMax = 0;
                        if(monitoring.length > 0) {
                            if(gudangData[a].uid == monitoring[a].gudang) {
                                nilaiMin = parseFloat(monitoring[a].min);
                                nilaiMax = parseFloat(monitoring[a].max);
                            }
                        }
						
						var newMonitoringMinimum = document.createElement("INPUT");
						$(newMonitoringCellGudangMinimum).append(newMonitoringMinimum);
						$(newMonitoringMinimum).addClass("form-control").inputmask({
							alias: 'decimal',
							rightAlign: true,
							placeholder: "0.00",
							prefix: "",
							autoGroup: false,
							digitsOptional: true
						}).val(nilaiMin);

						var newMonitoringMaximum = document.createElement("INPUT");
						$(newMonitoringCellGudangMaximum).append(newMonitoringMaximum);
						$(newMonitoringMaximum).addClass("form-control").inputmask({
							alias: 'decimal',
							rightAlign: true,
							placeholder: "0.00",
							prefix: "",
							autoGroup: false,
							digitsOptional: true
						}).val(nilaiMax);

						$(newMonitoringCellGudangSatuan).html($("#txt_satuan_terkecil").find("option:selected").text());

						$(newMonitoringRow).append(newMonitoringCellGudangID);
						$(newMonitoringRow).append(newMonitoringCellGudangName);
						$(newMonitoringRow).append(newMonitoringCellGudangMinimum);
						$(newMonitoringRow).append(newMonitoringCellGudangMaximum);
						$(newMonitoringRow).append(newMonitoringCellGudangSatuan);
						$("#table-monitoring tbody").append(newMonitoringRow);
					}*/
				},
				error: function(response) {
					console.log(response);
				}
			});
			return gudangData;
		}
		

		//==========================================================DASAR
		function saveDasar() {
			var nama = $("#txt_nama").val();
			var kode = $("#txt_kode").val();
			var kategori = $("#txt_kategori").val();
			var manufacture = $("#txt_manufacture").val();
			var keterangan = editorKeterangan.getData();

			/*console.log(nama);
			console.log(kode);
			console.log(kategori);
			console.log(manufacture);
			console.log(keterangan);*/
		}

		

		$("body").on("change", ".kategori_obat_selection", function() {
			var id = $(this).attr("id").split("_");
			id = id[id.length - 1];

			if($(this).is(":checked")) {
				if(selectedKategoriObat.indexOf(id) < 0) {
					selectedKategoriObat.push(id)
				}
			} else {
				selectedKategoriObat.splice(selectedKategoriObat.indexOf(id), 1);
			}
			$(".load-kategori-obat-badge").html("");
			for(var b = 0; b < selectedKategoriObat.length; b++) {
				$(".load-kategori-obat-badge").append("<div style=\"margin:5px;\" class=\"badge badge-info\"><i class=\"fa fa-tag\"></i>&nbsp;&nbsp;" + $("#label_kategori_obat_" + selectedKategoriObat[b]).html() + "</div>");
			}
		});

		$("body").on("keyup", "#txt_kode", function() {
			$(".label_kode").html($(this).val().toUpperCase());
		});

		$("body").on("keyup", "#txt_nama", function() {
			$(".label_nama").html($(this).val().toUpperCase());
		});

		$("body").on("change", "#txt_manufacture", function() {
			$(".label_manufacture").html($(this).find("option:selected").text().toUpperCase());
		});

		$("body").on("change", "#txt_kategori", function() {
			$(".label_kategori").html($(this).find("option:selected").text().toUpperCase());
		});

		$("body").on("keyup", ".satuan_rasio", function(){
			var id = $(this).attr("id").split("_");
			id = id[id.length - 1];
			if($("#row_satuan_" + id).hasClass("last-satuan") && parseFloat($(this).inputmask("unmaskedvalue")) > 0) {
				autoSatuan(selectedDariSatuanList);
			}
		});

		$("body").on("change", ".satuan_dari", function() {
			var id = $(this).attr("id").split("_");
			id = id[id.length - 1];
			checkSatuan(id, selectedDariSatuanList);
			if(selectedDariSatuanList.indexOf($(this).val()) < 0) {
				if($(this).val() != null) {
					selectedDariSatuanList.push($(this).val());		
				}
			}
			
		});

		$("body").on("change", ".satuan_ke", function() {
			var id = $(this).attr("id").split("_");
			id = id[id.length - 1];
			checkSatuan(id, selectedDariSatuanList);
		});

		var settedImage;
		var currentTab;
		
		$('body').on('mouseover', 'a[data-toggle="tab"]', function (e) {
			basic.croppie('result', {
				type: 'canvas',
				size: 'viewport'
			}).then(function (image) {
				settedImage = image;
			});
		});

		$('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
			currentTab = $(this).attr("href");
		});

        $("#txt_het").inputmask({
            alias: 'currency',
            rightAlign: true,
            placeholder: "0.00",
            prefix: "",
            autoGroup: false,
            digitsOptional: true
        }).addClass("form-control");


		$("body").on("click", ".saveData", function(){
			var nama = $("#txt_nama").val();
			var kode = $("#txt_kode").val();
            var het = $("#txt_het").inputmask("unmaskedvalue");
            var paketBarang = [];

            $("#table-paket tbody tr").each(function(e) {
                if(
                    $(this).find("td:eq(1) select").val() !== undefined && $(this).find("td:eq(1) select").val() !== null &&
                    $(this).find("td:eq(2) input").inputmask("unmaskedvalue") > 0
                ) {
                    paketBarang.push({
                        barang: $(this).find("td:eq(1) select").val(),
                        qty: $(this).find("td:eq(2) input").inputmask("unmaskedvalue")
                    });
                } else {
                    paketBarang.push({
                        barang: $(this).find("td:eq(1) select").val(),
                        qty: $(this).find("td:eq(2) input").inputmask("unmaskedvalue")
                    });
                }
            });

            /*console.clear();
            console.log(paketBarang);*/


			if(nama != "" && kode != "" && het > 0) {
				$(".action-panel").attr({
					"disabled": "disabled"
				});

				if(currentTab == "#tab-informasi" || currentTab == "#info-dasar-1" || currentTab == undefined) {
					basic.croppie('result', {
						type: 'canvas',
						size: 'viewport'
					}).then(function (image) {
						var kategori = $("#txt_kategori").val();
						var manufacture = $("#txt_manufacture").val();
						var keterangan = editorKeterangan.getData();
						var satuan_terkecil = $("#txt_satuan_terkecil").val();
						var listKategoriObat = selectedKategoriObat;
						
						var satuanKonversi = [];
						//Satuan
						$("#table-konversi-satuan tbody tr").each(function(){
							var dari = $(this).find("td:eq(1) select").val();
							var ke = $(this).find("td:eq(2) select").val();
							var rasio = $(this).find("td:eq(3) input").inputmask("unmaskedvalue");
							if(parseFloat(rasio) > 0 && dari != ke) {
								satuanKonversi.push({
									dari:dari,
									ke:ke,
									rasio: parseFloat(rasio)
								});
							}
						});

						var penjaminList = [];
						//Penjamin
						$("#table-penjamin tbody tr").each(function(){
							var id = $(this).attr("id").split("_");
							id = id[id.length - 1];
							var marginType = $(this).find("td:eq(2) select").val();
							var marginValue = $(this).find("td:eq(2) input").inputmask("unmaskedvalue");
							if(parseFloat(marginValue) > 0) {
								penjaminList.push({
									penjamin:id,
									marginType:marginType,
									marginValue:marginValue
								});
							}
						});

						var gudangMeta = [];
						//Rak Gudang
						$("#table-lokasi-gudang tbody tr").each(function(){
							var id = $(this).attr("id").split("_");
							id = id[id.length - 1];
							var lokasiRak = $(this).find("td:eq(2) input").val();
							gudangMeta.push({
								"gudang": id,
								"lokasi": lokasiRak
							});
						});

						var monitoring = [];
						//Monitoring
						$("#table-monitoring tbody tr").each(function(){
							var id = $(this).attr("id").split("_");
							id = id[id.length - 1];

							var min = $(this).find("td:eq(2) input").inputmask("unmaskedvalue");
							var max = $(this).find("td:eq(3) input").inputmask("unmaskedvalue");

							if(parseFloat(min) > 0 && parseFloat(max) > 0) {
								monitoring.push({
									gudang:id,
									min:min,
									max:max
								});
							}
						});

						var kandungan = [];
						$("#load-kandungan-obat tbody tr").each(function() {
						    if(
						        !$(this).hasClass("last_kandungan") &&
                                $(this).find("td:eq(1) input").val() !== ""
                            ) {
						        kandungan.push({
                                    kandungan: $(this).find("td:eq(1) input").val(),
                                    keterangan: $(this).find("td:eq(2) input").val()
                                });
                            }
                        });

                        $.ajax({
							url:__HOSTAPI__ + "/Inventori",
							async:false,
							beforeSend: function(request) {
								request.setRequestHeader("Authorization", "Bearer " + <?php echo json_encode($_SESSION["token"]); ?>);
							},
							data:{
								request:"edit_item",
								uid:UID,
								kode:kode,
								nama:nama,
								image:image,
                                het:het,
								kategori:kategori,
								keterangan:keterangan,
								manufacture:manufacture,
								satuan_terkecil:satuan_terkecil,
								listKategoriObat:listKategoriObat,
								satuanKonversi:satuanKonversi,
                                kandungan: kandungan,
								penjaminList:penjaminList,
								gudangMeta:gudangMeta,
								monitoring:monitoring,
                                paket:paketBarang
							},
							type:"POST",
							success:function(response) {
                                notification ("success", "Data berhasil diproses", 3000, "hasil_tambah");
                                /*if(response.response_package == 0) {
									notification ("success", "Data berhasil diproses", 3000, "hasil_tambah");
								} else {
									//console.log(response);
								}*/
								$(".action-panel").removeAttr("disabled");
							},
							error: function(response) {
								$(".action-panel").removeAttr("disabled");
								console.clear();

							}
						});




					});
				} else {
					var kategori = $("#txt_kategori").val();
					var manufacture = $("#txt_manufacture").val();
					var keterangan = editorKeterangan.getData();
					var satuan_terkecil = $("#txt_satuan_terkecil").val();
					var listKategoriObat = selectedKategoriObat;
					
					var satuanKonversi = [];
					//Satuan
					$("#table-konversi-satuan tbody tr").each(function() {
						var dari = $(this).find("td:eq(1) select").val();
						var ke = $(this).find("td:eq(2) select").val();
						var rasio = $(this).find("td:eq(3) input").inputmask("unmaskedvalue");
						if(parseFloat(rasio) > 0 && dari != ke) {
							satuanKonversi.push({
								dari:dari,
								ke:ke,
								rasio: parseFloat(rasio)
							});
						}
					});

					var penjaminList = [];
					//Penjamin
					$("#table-penjamin tbody tr").each(function() {
						var id = $(this).attr("id").split("_");
						id = id[id.length - 1];
						var marginType = $(this).find("td:eq(2) select").val();
						var marginValue = $(this).find("td:eq(3) input").inputmask("unmaskedvalue");
						if(parseFloat(marginValue) > 0) {
							penjaminList.push({
								penjamin:id,
								marginType:marginType,
								marginValue:marginValue
							});
						}
					});

					var gudangMeta = [];
					//Rak Gudang
					$("#table-lokasi-gudang tbody tr").each(function(){
						var id = $(this).attr("id").split("_");
						id = id[id.length - 1];
						var lokasiRak = $(this).find("td:eq(2) input").val();
						gudangMeta.push({
							"gudang": id,
							"lokasi": lokasiRak
						});
					});

					var monitoring = [];
					//Monitoring
					$("#table-monitoring tbody tr").each(function() {
						var id = $(this).attr("id").split("_");
						id = id[id.length - 1];

						var min = $(this).find("td:eq(2) input").inputmask("unmaskedvalue");
						var max = $(this).find("td:eq(3) input").inputmask("unmaskedvalue");

						if(parseFloat(min) > 0 && parseFloat(max) > 0) {
							monitoring.push({
								gudang:id,
								min:min,
								max:max
							});
						}
					});

                    var kandungan = [];
                    $("#load-kandungan-obat tbody tr").each(function() {
                        if(
                            !$(this).hasClass("last_kandungan") &&
                            $(this).find("td:eq(1) input").val() !== ""
                        ) {
                            kandungan.push({
                                kandungan: $(this).find("td:eq(1) input").val(),
                                keterangan: $(this).find("td:eq(2) input").val()
                            });
                        }
                    });
                    $.ajax({
						url:__HOSTAPI__ + "/Inventori",
						async:false,
						beforeSend: function(request) {
							request.setRequestHeader("Authorization", "Bearer " + <?php echo json_encode($_SESSION["token"]); ?>);
						},
						data:{
							request:"edit_item",
							uid:UID,
							kode:kode,
							nama:nama,
							image:settedImage,
                            het:het,
							kategori:kategori,
							keterangan:keterangan,
							manufacture:manufacture,
							satuan_terkecil:satuan_terkecil,
							listKategoriObat:listKategoriObat,
							satuanKonversi:satuanKonversi,
                            kandungan: kandungan,
							penjaminList:penjaminList,
							gudangMeta:gudangMeta,
							monitoring:monitoring,
                            paket:paketBarang
						},
						type:"POST",
						success:function(response) {
                            notification ("success", "Data berhasil diproses", 3000, "hasil_tambah");
							/*if(response.response_package == 0) {
								notification ("success", "Data berhasil diproses", 3000, "hasil_tambah");
							} else {
								notification ("danger", "Data gagal diproses", 3000, "hasil_tambah");
							}*/
							$(".action-panel").removeAttr("disabled");
						},
						error: function(response) {
							$(".action-panel").removeAttr("disabled");
							console.clear();
							console.log(response);
						}
					});
				}
			}
			$(".action-panel").removeAttr("disabled");
		});

	});
</script>