<?php

namespace PondokCoder;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use PondokCoder\Query as Query;
use PondokCoder\Authorization as Authorization;
use PondokCoder\QueryException as QueryException;
use PondokCoder\PO as PO;
use PondokCoder\Penjamin as Penjamin;
use PondokCoder\Pegawai as Pegawai;
use PondokCoder\Pasien as Pasien;
use PondokCoder\Unit as Unit;
use PondokCoder\Utility as Utility;

class Inventori extends Utility
{
    static $pdo;
    static $query;

    protected static function getConn()
    {
        return self::$pdo;
    }

    public function __construct($connection)
    {
        self::$pdo = $connection;
        self::$query = new Query(self::$pdo);
    }

    public function __GET__($parameter = array())
    {
        try {
            switch ($parameter[1]) {
                case 'kategori':
                    return self::get_kategori();
                    break;
                case 'kategori_detail':
                    return self::get_kategori_detail($parameter[2]);
                    break;
                case 'satuan':
                    return self::get_satuan();
                    break;
                case 'satuan_detail':
                    return self::get_satuan_detail($parameter[2]);
                    break;
                case 'gudang':
                    return self::get_gudang();
                    break;
                case 'gudang_detail':
                    return self::get_gudang_detail($parameter[2]);
                    break;
                case 'item_detail':
                    return self::get_item_detail($parameter[2]);
                    break;
                case 'kartu_stok':

                    $ItemDetail = self::get_item_detail($parameter[2]);
                    $ItemStokLog = self::get_item_stok_log($parameter[2], $parameter[3], $parameter[4], $parameter[5]);
                    $ItemDetail['response_data'][0]['log'] = $ItemStokLog['response_data'];
                    return $ItemDetail;

                    break;
                case 'manufacture':
                    return self::get_manufacture();
                    break;
                case 'manufacture_detail':
                    return self::get_manufacture_detail($parameter[2]);
                    break;
                case 'kategori_obat':
                    return self::get_kategori_obat();
                    break;
                case 'kategori_obat_detail':
                    return self::get_kategori_obat_detail($parameter[2]);
                    break;
                case 'kategori_per_obat':
                    return self::get_kategori_obat_item_parsed($parameter[2]);
                    break;
                case 'item_batch':
                    return self::get_item_batch($parameter[2]);
                    break;
                case 'get_amprah_detail':
                    return self::get_amprah_detail($parameter[2]);
                    break;
                case 'get_amprah_proses_detail':
                    return self::get_amprah_proses_detail($parameter[2]);
                    break;
                case 'get_stok':
                    return self::get_stok();
                    break;
                case 'get_stok_log':
                    return self::get_stok_log();
                    break;
                case 'get_opname_detail':
                    return self::get_opname_detail($parameter[2]);
                    break;
                case 'get_item_select2':
                    return self::get_item_select2($parameter);
                    break;
                case 'get_mutasi_item':
                    return self::get_mutasi_item($parameter);
                    break;
                case 'android_cari_produk':
                    return self::android_cari_produk($parameter);
                    break;
                case 'android_highlight':
                    return self::android_highlight($parameter);
                    break;

                case 'android_non_highlight':
                    return self::android_non_highlight($parameter);
                    break;

                case 'get_keranjang':
                    return self::get_keranjang($parameter);
                    break;

                default:
                    return self::get_item_select2($parameter);
            }
        } catch (QueryException $e) {
            return 'Error => ' . $e;
        }
    }

    public function __DELETE__($parameter = array())
    {
        return self::delete($parameter);
    }

    public function __POST__($parameter = array())
    {
        switch ($parameter['request']) {
            case 'migrate_harga':
                return self::migrate_harga($parameter);
                break;
            case 'tambah_kategori':
                return self::tambah_kategori($parameter);
                break;
            case 'edit_kategori':
                return self::edit_kategori($parameter);
                break;
            case 'tambah_satuan':
                return self::tambah_satuan($parameter);
                break;
            case 'tambah_gudang':
                return self::tambah_gudang($parameter);
                break;
            case 'edit_gudang':
                return self::edit_gudang($parameter);
                break;
            case 'edit_satuan':
                return self::edit_satuan($parameter);
                break;
            case 'tambah_manufacture':
                return self::tambah_manufacture($parameter);
                break;
            case 'edit_manufacture':
                return self::edit_manufacture($parameter);
                break;
            case 'tambah_item':
                return self::tambah_item($parameter);
                break;
            case 'edit_item':
                return self::edit_item($parameter);
                break;
            case 'tambah_kategori_obat':
                return self::tambah_kategori_obat($parameter);
                break;
            case 'edit_kategori_obat':
                return self::edit_kategori_obat($parameter);
                break;
            case 'tambah_amprah':
                return self::tambah_amprah($parameter);
                break;
            case 'get_amprah_request':
                return self::get_amprah_request($parameter);
                break;
            case 'get_amprah_request_finish':
                return self::get_amprah_request($parameter, 'S');
                break;
            case 'proses_amprah':
                return self::proses_amprah($parameter);
                break;
            case 'tambah_stok_awal':
                return self::tambah_stok_awal($parameter);
                break;
            case 'get_stok_gudang':
                return self::get_stok_gudang($parameter);
                break;
            case 'get_opname_history':
                return self::get_opname_history($parameter);
                break;
            case 'tambah_opname':
                return self::tambah_opname($parameter);
                break;
            case 'get_opname_detail_item':
                return self::get_opname_detail_item($parameter);
                break;
            case 'tambah_mutasi':
                return self::tambah_mutasi($parameter);
                break;
            case 'get_mutasi_request':
                return self::get_mutasi_request($parameter);
                break;
            case 'master_inv_import_fetch':
                return self::master_inv_import_fetch($parameter);
                break;
            case 'proceed_import_master_inv':
                return self::proceed_import_master_inv($parameter);
                break;
            case 'get_item_back_end':
                return self::get_item_back_end($parameter);
                break;
            case 'get_item_select2':
                return self::get_item_select2($parameter);
                break;
            case 'satu_harga_profit':
                return self::satu_harga_profit($parameter);
                break;
            case 'get_stok_back_end':
                return self::get_stok_back_end($parameter);
                break;
            case 'stok_import_fetch':
                return self::stok_import_fetch($parameter);
                break;
            case 'proceed_import_stok':
                return self::proceed_import_stok($parameter);
                break;
            case 'get_stok_log_backend':
                return self::get_stok_log_backend($parameter);
                break;

            case 'get_gudang_back_end':
                return self::get_gudang_back_end();
                break;

            case 'get_stok_batch_unit':
                return self::get_stok_batch_unit($parameter);
                break;

            case 'get_item_price':
                return self::get_item_price($parameter);
                break;

            case 'update_data_harga':
                return self::update_data_harga($parameter);
                break;

            case 'tambah_item_keranjang':
                return self::tambah_item_keranjang($parameter);
                break;

            case 'edit_item_keranjang':
                return self::edit_item_keranjang($parameter);
                break;

            case 'hapus_item_keranjang':
                return self::hapus_item_keranjang($parameter);
                break;

            case 'tambah_order_android':
                return self::tambah_order_android($parameter);
                break;

            case 'update_harga':
                return self::update_harga($parameter);
                break;

            default:
                return $parameter;
                break;
        }
    }

    private function update_harga($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);

        $check_harga = self::$query->select('master_inv_harga', array(
            'id',
            'produk',
            'member_type',
            'id_table',
            'het',
            'discount_type',
            'discount',
            'jual',
            'created_at',
            'updated_at',
            'cashback',
            'reward',
            'royalti',
            'insentif'
        ))
            ->where(array(
                'master_inv_harga.id_table' => '= ?',
                'AND',
                'master_inv_harga.produk' => '= ?',
                'AND',
                'master_inv_harga.deleted_at' => 'IS NULL'
            ), array(
                $parameter['id'],
                $parameter['produk']
            ))
            ->execute();
        if(count($check_harga['response_data']) > 0) {
            foreach ($check_harga['response_data'] as $key => $value) {
                if($value['member_type'] === 'M') {
                    $proceed_member = self::$query->update('master_inv_harga', array(
                        'produk' => $parameter['produk'],
                        'member_type' => 'M',
                        'id_table' => $parameter['id'],
                        'het' => $parameter['member_het'],
                        'discount_type' => $parameter['member_discount_type'],
                        'discount' => $parameter['member_discount'],
                        'jual' => $parameter['member_jual'],
                        'cashback' => $parameter['member_cashback'],
                        'reward' => $parameter['member_reward'],
                        'royalti' => $parameter['member_royalti'],
                        'insentif' => $parameter['member_insentif'],
                        'created_at' => parent::format_date(),
                        'updated_at' => parent::format_date()
                    ))
                        ->where(array(
                            'master_inv_harga.member_type' => '= ?',
                            'AND',
                            'master_inv_harga.id_table' => '= ?',
                            'AND',
                            'master_inv_harga.produk' => '= ?',
                            'AND',
                            'master_inv_harga.deleted_at' => 'IS NULL'
                        ), array(
                            'M',
                            $parameter['id'],
                            $parameter['produk']
                        ))
                        ->execute();
                } else {
                    $proceed_stokis = self::$query->update('master_inv_harga', array(
                        'produk' => $parameter['produk'],
                        'member_type' => 'S',
                        'id_table' => $parameter['id'],
                        'het' => $parameter['stokis_het'],
                        'discount_type' => $parameter['stokis_discount_type'],
                        'discount' => $parameter['stokis_discount'],
                        'jual' => $parameter['stokis_jual'],
                        'cashback' => $parameter['stokis_cashback'],
                        'reward' => $parameter['stokis_reward'],
                        'royalti' => $parameter['stokis_royalti'],
                        'insentif' => $parameter['stokis_insentif'],
                        'created_at' => parent::format_date(),
                        'updated_at' => parent::format_date()
                    ))
                        ->where(array(
                            'master_inv_harga.member_type' => '= ?',
                            'AND',
                            'master_inv_harga.id_table' => '= ?',
                            'AND',
                            'master_inv_harga.produk' => '= ?',
                            'AND',
                            'master_inv_harga.deleted_at' => 'IS NULL'
                        ), array(
                            'S',
                            $parameter['id'],
                            $parameter['produk']
                        ))
                        ->execute();
                }
            }
        } else {
            $proceed_member = self::$query->insert('master_inv_harga', array(
                'produk' => $parameter['produk'],
                'member_type' => 'M',
                'id_table' => $parameter['id'],
                'het' => $parameter['member_het'],
                'discount_type' => $parameter['member_discount_type'],
                'discount' => $parameter['member_discount'],
                'jual' => $parameter['member_jual'],
                'cashback' => $parameter['member_cashback'],
                'reward' => $parameter['member_reward'],
                'royalti' => $parameter['member_royalti'],
                'insentif' => $parameter['member_insentif'],
                'created_at' => parent::format_date(),
                'updated_at' => parent::format_date()
            ))
                ->execute();

            $proceed_stokis = self::$query->insert('master_inv_harga', array(
                'produk' => $parameter['produk'],
                'member_type' => 'S',
                'id_table' => $parameter['id'],
                'het' => $parameter['stokis_het'],
                'discount_type' => $parameter['stokis_discount_type'],
                'discount' => $parameter['stokis_discount'],
                'jual' => $parameter['stokis_jual'],
                'cashback' => $parameter['stokis_cashback'],
                'reward' => $parameter['stokis_reward'],
                'royalti' => $parameter['stokis_royalti'],
                'insentif' => $parameter['stokis_insentif'],
                'created_at' => parent::format_date(),
                'updated_at' => parent::format_date()
            ))
                ->execute();
        }

        return 1;
    }

    public function check_keranjang($parameter) {
        $data = self::$query->select('keranjang', array(
            'uid'
        ))
            ->where(array(
                'keranjang.member' => '= ?',
                'AND',
                'keranjang.status' => '= ?',
                'AND',
                'keranjang.deleted_at' => 'IS NULL'
            ), array(
                $parameter, 'N'
            ))
            ->execute();
        return $data;
    }

    private function hapus_item_keranjang($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);

        $check = self::check_keranjang($UserData['data']->uid);
        if(count($check['response_data']) > 0) {
            $target_uid = $check['response_data'][0]['uid'];
            $proceed = self::$query->delete('keranjang_detail')
                ->where(array(
                    'keranjang_detail.keranjang' => '= ?',
                    'AND',
                    'keranjang_detail.produk' => '= ?',
                    'AND',
                    'keranjang_detail.deleted_at' => 'IS NULL'
                ), array(
                    $target_uid, $parameter['uid_barang']
                ))
                ->execute();
            unset($proceed['response_query']);
            unset($proceed['response_values']);
            return $proceed;
        } else {
            return array(
                'response_result' => 0,
                'response_message' => 'Item tidak ditemukan'
            );
        }
    }

    private function edit_item_keranjang($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);

        $check = self::check_keranjang($UserData['data']->uid);
        if(count($check['response_data']) > 0) {
            $target_uid = $check['response_data'][0]['uid'];

            $proceedDetail = self::$query->update('keranjang_detail', array(
                'jumlah' => $parameter['qty']
            ))
                ->where(array(
                    'keranjang_detail.keranjang' => '= ?',
                    'AND',
                    'keranjang_detail.produk' => '= ?'
                ), array(
                    $target_uid, $parameter['uid_barang']
                ))
                ->execute();
            return array(
                'response_result' => ($proceedDetail['response_result'] > 0) ? 1 : 0,
                'response_message' => ($proceedDetail['response_result'] > 0) ? 'Keranjang Berhasil Diubah' : 'Keranjang Gagal Diubah'
            );
        } else {
            return array(
                'response_result' => 0,
                'response_message' => 'Item tidak ditemukan'
            );
        }


    }

    private function tambah_item_keranjang($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);

        $check = self::check_keranjang($UserData['data']->uid);
        if(count($check['response_data']) > 0) {
            $target_uid = $check['response_data'][0]['uid'];
            $proceed = self::$query->update('keranjang', array(
                'updated_at' => parent::format_date()
            ))
                ->where(array(
                    'keranjang.uid' => '= ?',
                    'AND',
                    'keranjang.deleted_at' => 'IS NULL'
                ), array(
                    $target_uid
                ))
                ->execute();
        } else {
            $target_uid = parent::gen_uuid();

            $proceed = self::$query->insert('keranjang', array(
                'uid' => $target_uid,
                'member' => $UserData['data']->uid,
                'status' => 'N',
                'created_at' => parent::format_date(),
                'updated_at' => parent::format_date()
            ))
                ->execute();
        }

        if($proceed['response_result'] > 0) {
            //check detail
            $checkDetail = self::$query->select('keranjang_detail', array(
                'id'
            ))
                ->where(array(
                    'keranjang_detail.keranjang' => '= ?',
                    'AND',
                    'keranjang_detail.produk' => '= ?'
                ), array(
                    $target_uid, $parameter['uid_barang']
                ))
                ->execute();

            if(count($checkDetail['response_data']) > 0) {
                $proceedDetail = self::$query->update('keranjang_detail', array(
                    'jumlah' => $parameter['qty'],
                    'deleted_at' => NULL
                ))
                    ->where(array(
                        'keranjang_detail.keranjang' => '= ?',
                        'AND',
                        'keranjang_detail.produk' => '= ?'
                    ), array(
                        $target_uid, $parameter['uid_barang']
                    ))
                    ->execute();
            } else {
                $detailProduk = self::get_item_detail($parameter['uid_barang'])['response_data'];
                $proceedDetail = self::$query->insert('keranjang_detail', array(
                    'produk' => $parameter['uid_barang'],
                    'jumlah' => $parameter['qty'],
                    'keranjang' => $target_uid,
                    'het' => floatval($detailProduk['het']),
                    'harga' => (($UserData['data']->jenis_member === 'M') ? floatval($detailProduk['harga']['harga_akhir_member']) : floatval($detailProduk['harga']['harga_akhir_stokis'])),
                    'jenis_member' => $UserData['data']->jenis_member,
                    'created_at' => parent::format_date(),
                    'updated_at' => parent::format_date()
                ))
                    ->execute();
            }
        }

        return array(
            'response_result' => ($proceed['response_result'] > 0 && $proceedDetail['response_result'] > 0) ? 1 : 0,
            'response_message' => ($proceed['response_result'] > 0 && $proceedDetail['response_result'] > 0) ? 'Keranjang Berhasil Ditambahkan' : 'Keranjang Gagal Ditambahkan'
        );
    }

    private function tambah_order($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);
        //Get Keranjang

        //$proceed = self::$query->insert('');
        return array();
    }

    private function tambah_order_android($parameter) {
        $parameter['created_on'] = 'A';
        return self::tambah_order($parameter);
    }


    private function get_keranjang($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);
        //$Customer = new Membership(self::$pdo);
        $Inventori = new Inventori(self::$pdo);
        $data = self::$query->select('keranjang', array(
            'uid',
            'status',
            'member'
        ))
            ->where(array(
                'keranjang.member' => '= ?',
                'AND',
                'keranjang.deleted_at' => 'IS NULL'
            ), array(
                $UserData['data']->uid
            ))
            ->execute();
        foreach ($data['response_data'] as $key => $value) {

            //$CustomerInfo = $Customer->customer_detail($value['member'])['response_data'][0];
            $detail = self::$query->select('keranjang_detail', array(
                'id',
                'produk',
                'jumlah',
                'keranjang',
                'het',
                'harga',
                'jenis_member'
            ))
                ->where(array(
                    'keranjang_detail.keranjang' => '= ?',
                    'AND',
                    'keranjang_detail.deleted_at' => 'IS NULL'
                ), array(
                    $value['uid']
                ))
                ->execute();
            foreach ($detail['response_data'] as $dKey => $dValue) {
                $detailProduk = self::get_item_detail($dValue['produk'])['response_data'];
                //$detail['response_data'][$dKey]['item'] = $detailProduk;

                $detailProduk['nama_produk'] = strtoupper($detailProduk['nama']);
                $detail['response_data'][$dKey]['harga'] = ($dValue['jenis_member'] === 'M') ? floatval($detailProduk['harga']['harga_akhir_member']) : floatval($detailProduk['harga']['harga_akhir_stokis']);
                unset($detailProduk['het']);
                unset($detailProduk['harga']);
                unset($detailProduk['text']);
                unset($detailProduk['kategori_obat']);

                foreach ($detailProduk as $pKey => $pValue) {
                    $detail['response_data'][$dKey][$pKey] = $pValue;
                }

                $detail['response_data'][$dKey]['satuan_terkecil'] = $dValue['satuan_terkecil_info']['nama'];
                $detail['response_data'][$dKey]['qty'] = floatval($dValue['jumlah']);
                $detail['response_data'][$dKey]['het'] = floatval($dValue['het']);

                //

                unset($detail['response_data'][$dKey]['jumlah']);
                unset($detail['response_data'][$dKey]['nama']);
                unset($detail['response_data'][$dKey]['satuan_terkecil_info']);
                unset($detail['response_data'][$dKey]['produk']);
                unset($detail['response_data'][$dKey]['keranjang']);
            }
            $data['response_data'][$key]['list_items'] = $detail['response_data'];
        }
        unset($data['response_query']);
        unset($data['response_values']);
        $data['response_message'] = (count($data['response_data']) > 0) ? 'Berhasil' : 'Data tidak ditemukan';
        $data['token'] = $parameter[count($parameter) - 1];
        return $data;
    }

    private function android_highlight($parameter) {
        $Authorization = new Authorization();
        if(!empty($Authorization::$Bearer)) {
            $UserData = $Authorization->readBearerToken($parameter['access_token']);
        } else {
            //var_dump($Authorization::$Bearer);
        }

        $data = self::$query
            ->select('master_inv', array(
                'uid',
                'kode_barang',
                'nama as nama_produk',
                //'kategori',
                //'satuan_terkecil',
                //'het',
                //'created_at',
                //'updated_at'
            ))
            ->where(array(
                'master_inv.deleted_at' => 'IS NULL'
            ))
            ->order(array(
                'master_inv.created_at' => 'DESC'
            ))
            ->limit(3)
            ->execute();

        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['rating'] = 5;
            if(file_exists('../images/produk/' . $value['uid'] . '.png')) {
                $data['response_data'][$key]['url_gambar'] = __HOST__ . 'images/produk/' . $value['uid'] . '.png';
            } else {
                $data['response_data'][$key]['url_gambar'] = __HOST__ . 'images/product.png';
            }
            /*$dataHarga = self::$query->select('strategi_penjualan', array(
                'id', 'produk', 'tanggal',
                'member_cashback', 'member_royalti', 'member_reward', 'member_insentif_personal',
                'stokis_cashback', 'stokis_royalti', 'stokis_reward', 'stokis_insentif_personal',
                'harga_jual_member',
                'discount_type_member',
                'discount_member',
                'harga_akhir_member',

                'harga_jual_stokis',
                'discount_type_stokis',
                'discount_stokis',
                'harga_akhir_stokis'
            ))
                ->where(array(
                    'strategi_penjualan.produk' => '= ?',
                    'AND',
                    'strategi_penjualan.tanggal' => '= ?',
                    'AND',
                    'strategi_penjualan.deleted_at' => 'IS NULL'
                ), array(
                    $value['uid'],
                    date('Y-m-d')
                ))
                ->execute();

            $data['response_data'][$key]['harga'] = ($UserData['data']->jenis_member === 'M') ? floatval($dataHarga['response_data'][0]['harga_akhir_member']) : floatval($dataHarga['response_data'][0]['harga_akhir_stokis']);*/


            $harga_builder = array();

            $dataHarga = self::$query->select('master_inv_harga', array(
                'id', 'produk',
                'member_type',
                'id_table',
                'het',
                'discount_type',
                'discount',
                'jual',
                'cashback',
                'royalti',
                'reward',
                'insentif'
            ))
                ->order(array(
                    'created_at' => 'DESC'
                ))
                ->limit(2)
                ->where(array(
                    'master_inv_harga.produk' => '= ?',
                    'AND',
                    'master_inv_harga.deleted_at' => 'IS NULL'
                ), array(
                    $value['uid']
                ))
                ->execute();

            foreach ($dataHarga['response_data'] as $HKey => $HValue) {
                if(!isset($harga_builder['cashback'])) {
                    $harga_builder['jual'] = 0;
                    $harga_builder['cashback'] = 0;
                    $harga_builder['reward'] = 0;
                    $harga_builder['royalti'] = 0;
                    $harga_builder['insentif'] = 0;
                }

                if($HValue['member_type'] === $UserData['data']->jenis_member) {

                    $harga_builder['jual'] = $HValue['jual'];
                    $harga_builder['cashback'] = $HValue['cashback'];
                    $harga_builder['reward'] = $HValue['reward'];
                    $harga_builder['royalti'] = $HValue['royalti'];
                    $harga_builder['insentif'] = $HValue['insentif'];
                }
            }

            $data['response_data'][$key]['harga'] = floatval($harga_builder['jual']);
        }
        return $data;
    }

    private function android_non_highlight($parameter) {
        $Authorization = new Authorization();
        if(!empty($Authorization::$Bearer)) {
            $UserData = $Authorization->readBearerToken($parameter['access_token']);
        } else {
            //var_dump($Authorization::$Bearer);
        }

        $high = self::android_highlight($parameter);
        $high_data = array();
        $not_high = array();
        foreach ($high['response_data'] as $key => $value) {
            array_push($high_data, $value['uid']);
        }

        $data = self::$query
            ->select('master_inv', array(
                'uid',
                'kode_barang',
                'nama as nama_produk',
                //'kategori',
                //'satuan_terkecil',
                //'het',
                //'created_at',
                //'updated_at'
            ))
            ->where(array(
                'master_inv.deleted_at' => 'IS NULL'
            ))
            ->order(array(
                'master_inv.created_at' => 'DESC'
            ))
            ->execute();
        foreach ($data['response_data'] as $key => $value) {
            //$data['response_data'][$key]['rating'] = 5;
            if(!in_array($high_data, $value['uid'])) {
                $value['rating'] = 5;

                if(file_exists('../images/produk/' . $value['uid'] . '.png')) {
                    $value['url_gambar'] = __HOST__ . 'images/produk/' . $value['uid'] . '.png';
                } else {
                    $value['url_gambar'] = __HOST__ . 'images/product.png';
                }

                $harga_builder = array();

                $dataHarga = self::$query->select('master_inv_harga', array(
                    'id', 'produk',
                    'member_type',
                    'id_table',
                    'het',
                    'discount_type',
                    'discount',
                    'jual',
                    'cashback',
                    'royalti',
                    'reward',
                    'insentif'
                ))
                    ->order(array(
                        'created_at' => 'DESC'
                    ))
                    ->where(array(
                        'master_inv_harga.produk' => '= ?',
                        'AND',
                        'master_inv_harga.deleted_at' => 'IS NULL'
                    ), array(
                        $value['uid']
                    ))
                    ->execute();

                foreach ($dataHarga['response_data'] as $HKey => $HValue) {
                    if(!isset($harga_builder['cashback'])) {
                        $harga_builder['jual'] = 0;
                        $harga_builder['cashback'] = 0;
                        $harga_builder['reward'] = 0;
                        $harga_builder['royalti'] = 0;
                        $harga_builder['insentif'] = 0;
                    }

                    if($HValue['member_type'] === $UserData['data']->jenis_member) {
                        $harga_builder['jual'] = $HValue['jual'];
                        $harga_builder['cashback'] = $HValue['cashback'];
                        $harga_builder['reward'] = $HValue['reward'];
                        $harga_builder['royalti'] = $HValue['royalti'];
                        $harga_builder['insentif'] = $HValue['insentif'];
                    }
                }

                $value['harga'] = floatval($harga_builder['jual']);

                /*$dataHarga = self::$query->select('strategi_penjualan', array(
                    'id', 'produk', 'tanggal',
                    'member_cashback', 'member_royalti', 'member_reward', 'member_insentif_personal',
                    'stokis_cashback', 'stokis_royalti', 'stokis_reward', 'stokis_insentif_personal',
                    'harga_jual_member',
                    'discount_type_member',
                    'discount_member',
                    'harga_akhir_member',

                    'harga_jual_stokis',
                    'discount_type_stokis',
                    'discount_stokis',
                    'harga_akhir_stokis'
                ))
                    ->where(array(
                        'strategi_penjualan.produk' => '= ?',
                        'AND',
                        'strategi_penjualan.tanggal' => '= ?',
                        'AND',
                        'strategi_penjualan.deleted_at' => 'IS NULL'
                    ), array(
                        $value['uid'],
                        date('Y-m-d')
                    ))
                    ->execute();*/

                //$value['harga'] = ($UserData['data']->jenis_member === 'M') ? floatval($dataHarga['response_data'][0]['harga_akhir_member']) : floatval($dataHarga['response_data'][0]['harga_akhir_stokis']);

                array_push($not_high, $value);
            }
        }
        $data['response_data'] = $not_high;
        return $data;
    }

    private function android_cari_produk($parameter) {
        $Authorization = new Authorization();
        $token = $parameter[count($parameter) - 1];
        if($token !== 'not_valid_token') {
            $UserData = $Authorization->readBearerToken($token);
        }


        $data = self::$query->select('master_inv', array(
                'uid',
                'kode_barang',
                'nama as nama_produk',
                'kategori',
                'satuan_terkecil',
                'het',
                'created_at',
                'updated_at'
            ))
            ->where(array(
                'master_inv.deleted_at' => 'IS NULL',
                'AND',
                '(master_inv.kode_barang' => 'ILIKE ' . '\'%' . $_GET['query_string'] . '%\'',
                'OR',
                'master_inv.nama' => 'ILIKE ' . '\'%' . $_GET['query_string'] . '%\')'
            ))
            ->execute();

        $autonum = 1;
        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['autonum'] = $autonum;
            $data['response_data'][$key]['kode_barang'] = strtoupper($value['kode_barang']);
            $data['response_data'][$key]['nama_produk'] = strtoupper($value['nama_produk']);
            $data['response_data'][$key]['rating'] = 5.0;
            if(file_exists('../images/produk/' . $value['uid'] . '.png')) {
                $data['response_data'][$key]['url_gambar'] = __HOST__ . 'images/produk/' . $value['uid'] . '.png';
            } else {
                $data['response_data'][$key]['url_gambar'] = __HOST__ . 'images/product.png';
            }
            $data['response_data'][$key]['satuan_terkecil'] = self::get_satuan_detail($value['satuan_terkecil'])['response_data'][0]['nama'];
            $data['response_data'][$key]['kategori'] = self::get_kategori_detail($value['kategori'])['response_data'][0]['nama'];


            /*$dataHarga = self::$query->select('strategi_penjualan', array(
                'id', 'produk', 'tanggal',
                'member_cashback', 'member_royalti', 'member_reward', 'member_insentif_personal',
                'stokis_cashback', 'stokis_royalti', 'stokis_reward', 'stokis_insentif_personal',
                'harga_jual_member',
                'discount_type_member',
                'discount_member',
                'harga_akhir_member',

                'harga_jual_stokis',
                'discount_type_stokis',
                'discount_stokis',
                'harga_akhir_stokis'
            ))
                ->where(array(
                    'strategi_penjualan.produk' => '= ?',
                    'AND',
                    'strategi_penjualan.tanggal' => '= ?',
                    'AND',
                    'strategi_penjualan.deleted_at' => 'IS NULL'
                ), array(
                    $value['uid'],
                    date('Y-m-d')
                ))
                ->execute();
            $data['response_data'][$key]['harga_member'] = floatval($dataHarga['response_data'][0]['harga_jual_member']);
            $data['response_data'][$key]['harga_stokis'] = floatval($dataHarga['response_data'][0]['harga_jual_stokis']);*/



            $harga_builder = array();
            $harga_stokis = 0;
            $harga_member = 0;
            $het_member = 0;
            $het_stokis = 0;

            $dataHarga = self::$query->select('master_inv_harga', array(
                'id', 'produk',
                'member_type',
                'id_table',
                'het',
                'discount_type',
                'discount',
                'jual',
                'cashback',
                'royalti',
                'reward',
                'insentif'
            ))
                ->order(array(
                    'created_at' => 'DESC'
                ))
                ->limit(2)
                ->where(array(
                    'master_inv_harga.produk' => '= ?',
                    'AND',
                    'master_inv_harga.deleted_at' => 'IS NULL'
                ), array(
                    $value['uid']
                ))
                ->execute();

            foreach ($dataHarga['response_data'] as $HKey => $HValue) {
                if(!isset($harga_builder['cashback'])) {
                    $harga_builder['jual'] = 0;
                    $harga_builder['cashback'] = 0;
                    $harga_builder['reward'] = 0;
                    $harga_builder['royalti'] = 0;
                    $harga_builder['insentif'] = 0;
                }
                if($HValue['member_type'] === $UserData['data']->jenis_member) {
                    $harga_builder['jual'] = $HValue['jual'];
                    $harga_builder['cashback'] = $HValue['cashback'];
                    $harga_builder['reward'] = $HValue['reward'];
                    $harga_builder['royalti'] = $HValue['royalti'];
                    $harga_builder['insentif'] = $HValue['insentif'];
                }

                if($HValue['member_type'] === 'M') {
                    $harga_member = $HValue['jual'];
                    $het_member = $HValue['het'];
                } else {
                    $harga_stokis = $HValue['jual'];
                    $het_stokis = $HValue['het'];
                }
            }

            $data['response_data'][$key]['het'] = floatval(($UserData['data']->jenis_member === 'M') ? $het_member : $het_stokis);
            $data['response_data'][$key]['harga_member'] = floatval($harga_member);
            $data['response_data'][$key]['harga_stokis'] = floatval($harga_stokis);


            $autonum++;
        }

        $data['response_message'] = (count($data['response_data']) > 0) ? 'Data Tersedia' : 'Data Tidak Tersedia';
        return $data;
    }

    private function update_data_harga($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);


        $checker = self::$query->select('strategi_penjualan', array(
            'id'
        ))
            ->where(array(
                'strategi_penjualan.produk' => '= ?',
                'AND',
                'strategi_penjualan.tanggal' => '= ?'
            ), array(
                $parameter['produk'],
                date('Y-m-d', strtotime($parameter['tanggal']))
            ))
            ->execute();

        if(count($checker['response_data']) > 0) {
            //update
            $process = self::$query->update('strategi_penjualan', array(
                'member_cashback' => $parameter['m_cb'],
                'member_royalti' => $parameter['m_ry'],
                'member_reward' => $parameter['m_rw'],
                'member_insentif_personal' => $parameter['m_ip'],

                'stokis_cashback' => $parameter['s_cb'],
                'stokis_royalti' => $parameter['s_ry'],
                'stokis_reward' => $parameter['s_rw'],
                'stokis_insentif_personal' => $parameter['s_ip'],

                'harga_jual_member' => $parameter['m_hj'],
                'discount_type_member' => $parameter['m_dt'],
                'discount_member' => $parameter['m_d'],
                'harga_akhir_member' => $parameter['m_ha'],

                'harga_jual_stokis' => $parameter['s_hj'],
                'discount_type_stokis' => $parameter['s_dt'],
                'discount_stokis' => $parameter['s_d'],
                'harga_akhir_stokis' => $parameter['s_ha'],


                'updated_at' => parent::format_date(),
                'deleted_at' => NULL
            ))
                ->where(array(
                    'strategi_penjualan.id' => '= ?',
                    'AND',
                    'strategi_penjualan.produk' => '= ?',
                    'AND',
                    'strategi_penjualan.tanggal' => '= ?'
                ), array(
                    $checker['response_data'][0]['id'],
                    $parameter['produk'],
                    date('Y-m-d', strtotime($parameter['tanggal']))
                ))
                ->execute();

            if($process['response_result'] > 0) {
                $log = parent::log(array(
                    'type' => 'activity',
                    'column' => array(
                        'unique_target',
                        'user_uid',
                        'table_name',
                        'action',
                        'old_value',
                        'new_value',
                        'logged_at',
                        'status',
                        'login_id'
                    ),
                    'value' => array(
                        $checker['response_data'][0]['id'],
                        $UserData['data']->uid,
                        'strategi_penjualan',
                        'U',
                        json_encode($checker['response_data'][0]),
                        json_encode($parameter),
                        parent::format_date(),
                        'N',
                        $UserData['data']->log_id
                    ),
                    'class' => __CLASS__
                ));
            }

        } else {
            //insert
            $process = self::$query->insert('strategi_penjualan', array(
                'produk' => $parameter['produk'],
                'tanggal' => date('Y-m-d', strtotime($parameter['tanggal'])),
                'member_cashback' => $parameter['m_cb'],
                'member_royalti' => $parameter['m_ry'],
                'member_reward' => $parameter['m_rw'],
                'member_insentif_personal' => $parameter['m_ip'],

                'stokis_cashback' => $parameter['s_cb'],
                'stokis_royalti' => $parameter['s_ry'],
                'stokis_reward' => $parameter['s_rw'],
                'stokis_insentif_personal' => $parameter['s_ip'],

                'harga_jual_member' => $parameter['m_hj'],
                'discount_type_member' => $parameter['m_dt'],
                'discount_member' => $parameter['m_d'],
                'harga_akhir_member' => $parameter['m_ha'],

                'harga_jual_stokis' => $parameter['s_hj'],
                'discount_type_stokis' => $parameter['s_dt'],
                'discount_stokis' => $parameter['s_d'],
                'harga_akhir_stokis' => $parameter['s_ha'],

                'created_at' => parent::format_date(),
                'updated_at' => parent::format_date()
            ))
                ->execute();

            if($process['response_result'] > 0) {
                $log = parent::log(array(
                    'type' => 'activity',
                    'column' => array(
                        'unique_target',
                        'user_uid',
                        'table_name',
                        'action',
                        'logged_at',
                        'status',
                        'login_id'
                    ),
                    'value' => array(
                        $process['response_unique'],
                        $UserData['data']->uid,
                        'strategi_penjualan',
                        'I',
                        parent::format_date(),
                        'N',
                        $UserData['data']->log_id
                    ),
                    'class' => __CLASS__
                ));
            }
        }

        return $process;
    }






    private  function get_item_price($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);

        if (isset($parameter['search']['value']) && !empty($parameter['search']['value'])) {
            $paramData = array(
                'master_inv.deleted_at' => 'IS NULL',
                'AND',
                'master_inv.nama' => 'ILIKE ' . '\'%' . $parameter['search']['value'] . '%\''
            );

            $paramValue = array();
        } else {
            $paramData = array(
                'master_inv.deleted_at' => 'IS NULL'
            );

            $paramValue = array();
        }


        if ($parameter['length'] < 0) {
            $data = self::$query->select('master_inv', array(
                'uid',
                'kode_barang',
                'nama',
                'kategori',
                'het',
                'satuan_terkecil',
                'created_at',
                'updated_at'
            ))
                ->where($paramData, $paramValue)
                ->execute();
        } else {
            $data = self::$query->select('master_inv', array(
                'uid',
                'kode_barang',
                'nama',
                'kategori',
                'het',
                'satuan_terkecil',
                'created_at',
                'updated_at'
            ))
                ->where($paramData, $paramValue)
                ->offset(intval($parameter['start']))
                ->limit(intval($parameter['length']))
                ->execute();
        }

        $data['response_draw'] = $parameter['draw'];
        $autonum = intval($parameter['start']) + 1;
        foreach ($data['response_data'] as $key => $value) {


            $data['response_data'][$key]['autonum'] = $autonum;
            $allowEdit = false;
            //Get Harga Per Tanggal
            $dataHarga = self::$query->select('strategi_penjualan', array(
                'id', 'produk', 'tanggal',
                'member_cashback', 'member_royalti', 'member_reward', 'member_insentif_personal',
                'stokis_cashback', 'stokis_royalti', 'stokis_reward', 'stokis_insentif_personal',
                'harga_jual_member',
                'discount_type_member',
                'discount_member',
                'harga_akhir_member',

                'harga_jual_stokis',
                'discount_type_stokis',
                'discount_stokis',
                'harga_akhir_stokis'
            ))
                ->where(array(
                    'strategi_penjualan.produk' => '= ?',
                    'AND',
                    'strategi_penjualan.tanggal' => '= ?',
                    'AND',
                    'strategi_penjualan.deleted_at' => 'IS NULL'
                ), array(
                    $value['uid'],
                    date('Y-m-d', strtotime($parameter['tanggal']))
                ))
                ->execute();
            $data['response_data'][$key]['harga'] = $dataHarga['response_data'][0];
            if(date('Y-m-d') <= date('Y-m-d', strtotime($parameter['tanggal']))) {
                /*if(count($dataHarga['response_data']) > 0) {
                    $allowEdit = true;
                } else {
                    $allowEdit = false;
                }*/
                $allowEdit = true;
            } else {

                $allowEdit = false;
            }

            $data['response_data'][$key]['allow_edit'] = $allowEdit;
            $data['response_data'][$key]['satuan_terkecil'] = self::get_satuan_detail($value['satuan_terkecil'])['response_data'][0];
            $data['response_data'][$key]['kategori'] = self::get_kategori_detail($value['kategori'])['response_data'][0];

            $autonum++;
        }

        $itemTotal = self::$query->select('master_inv', array(
            'uid'
        ))
            ->where($paramData, $paramValue)
            ->execute();

        $data['recordsTotal'] = count($itemTotal['response_data']);
        $data['recordsFiltered'] = count($itemTotal['response_data']);
        $data['length'] = intval($parameter['length']);
        $data['start'] = intval($parameter['start']);

        return $data;
    }





    private function stok_import_fetch($parameter)
    {
        if (!empty($_FILES['csv_file']['name'])) {
            $unique_name = array();

            $file_data = fopen($_FILES['csv_file']['tmp_name'], 'r');
            $column = fgetcsv($file_data); //array_head
            $row_data = array();
            while ($row = fgetcsv($file_data)) {
                if (!in_array($row[0], $unique_name)) {
                    array_push($unique_name, $row[0]);
                    $column_builder = array();
                    foreach ($column as $key => $value) {
                        $column_builder[$value] = $row[$key];
                    }
                    array_push($row_data, $column_builder);
                }
            }

            $build_col = array();
            foreach ($column as $key => $value) {
                array_push($build_col, array("data" => $value));
            }

            $output = array(
                'column' => $column,
                'row_data' => $row_data,
                'column_builder' => $build_col
            );
            return $output;
        }
    }

    private function proceed_import_stok($parameter)
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);
        $PO = parent::gen_uuid();
        $PODetailResult = array();

        $duplicate_row = array();
        $non_active = array();
        $success_proceed = 0;
        $proceed_data = array();
        $all_data = array();

        $total_all = 0;



        foreach ($parameter['data_import'] as $key => $value) {

            $targettedObat = '';
            $targettedKategori = '';
            $targettedSupplier = '';
            $targettedSatuan = '';
            $targettedBatch = '';


            if($value['kategori'] != '') {
                $checkKategori = self::$query->select('master_inv_kategori', array(
                    'uid',
                    'nama'
                ))
                    ->where(array(
                        'master_inv_kategori.nama' => '= ?'
                    ), array(
                        $value['kategori']
                    ))
                    ->execute();
                if(count($checkKategori['response_data']) > 0) {
                    $targettedKategori = $checkKategori['response_data'][0]['uid'];
                } else {
                    $targettedKategori = parent::gen_uuid();
                    $kategoriBaru = self::$query->insert('master_inv_kategori', array(
                        'uid' => $targettedKategori,
                        'nama' => $value['kategori'],
                        'created_at' => parent::format_date(),
                        'updated_at' => parent::format_date()
                    ))
                        ->execute();
                }
            }

            if($value['supplier'] != '') {
                $checkSupplier = self::$query->select('master_supplier', array(
                    'uid'
                ))
                    ->where(array(
                        'master_supplier.nama' => '= ?'
                    ), array(
                        $value['supplier']
                    ))
                    ->execute();
                if(count($checkSupplier['response_data']) > 0) {
                    $targettedSupplier = $checkSupplier['response_data'][0]['uid'];
                } else {
                    $targettedSupplier = parent::gen_uuid();
                    $new_supplier = self::$query->insert('master_supplier', array(
                        'uid' => $targettedSupplier,
                        'nama' => $value['supplier'],
                        'created_at' => parent::format_date(),
                        'updated_at' => parent::format_date()
                    ))
                        ->execute();
                }
            }

            if($value['satuan'] != '') {
                //Satuan Obat
                $checkSatuan = self::$query->select('master_inv_satuan', array(
                    'uid',
                    'nama'
                ))
                    ->where(array(
                        'master_inv_satuan.nama' => '= ?'
                    ), array(
                        $value['satuan']
                    ))
                    ->execute();

                if(count($checkSatuan['response_data']) > 0) {
                    $targettedSatuan = $checkSatuan['response_data'][0]['uid'];
                } else {
                    $targettedSatuan = parent::gen_uuid();
                    $new_satuan = self::$query->insert('master_inv_satuan', array(
                        'uid' => $targettedSatuan,
                        'nama' => $value['satuan'],
                        'created_at' => parent::format_date(),
                        'updated_at' => parent::format_date()
                    ))
                        ->execute();
                }
            }

            if($value['nama'] != '') {
                $checkObat = self::$query->select('master_inv', array(
                    'uid',
                    'nama'
                ))
                    ->where(array(
                        'master_inv.nama' => '= ?'
                    ), array(
                        $value['nama']
                    ))
                    ->execute();
                if(count($checkObat['response_data']) > 0) {
                    $targettedObat = $checkObat['response_data'][0]['uid'];

                    //Update Info Obat
                    $updateObat = self::$query->update('master_inv', array(
                        'kategori' => $targettedKategori,
                        'satuan_terkecil' => $targettedSatuan,
                        'keterangan' => $value['nama_rko'],
                        'updated_at' => parent::format_date()
                    ))
                        ->where(array(
                            'master_inv.uid' => '= ?',
                            'AND',
                            'master_inv.deleted_at' => ' IS NULL'
                        ), array(
                            $targettedObat
                        ))
                        ->execute();
                } else {
                    //New Inventori
                    if($value['nama'] != '') {
                        $targettedObat = parent::gen_uuid();
                        $new_obat = self::$query->insert('master_inv', array(
                            'uid' => $targettedObat,
                            'nama' => $value['nama'],
                            'kategori' => $targettedKategori,
                            'keterangan' => $value['nama_rko'],
                            'satuan_terkecil' => $targettedSatuan,
                            'created_at' => parent::format_date(),
                            'updated_at' => parent::format_date()
                        ))
                            ->execute();

                        if($new_obat['response_result'] > 0) {
                            $success_proceed += 1;
                        }

                        array_push($proceed_data, $new_obat);
                    }
                }
            }



            if($targettedKategori === __UID_KATEGORI_OBAT) {
                $checkItem = array();

                if($value['generik'] === 'GENERIK') {
                    array_push($checkItem, __UID_GENERIK__);
                }

                if($value['antibiotik'] === 'ANTIBIOTIK') {
                    array_push($checkItem, __UID_ANTIBIOTIK__);
                }

                if($value['narkotika'] === 'NARKOTIKA') {
                    array_push($checkItem, __UID_NARKOTIKA__);
                }

                if($value['psikotropika'] === 'PSIKOTROPIKA') {
                    array_push($checkItem, __UID_PSIKOTROPIKA__);
                }
            }

            if($value['batch'] != '') {
                $checkBatch = self::$query->select('inventori_batch', array(
                    'uid'
                ))
                    ->where(array(
                        'inventori_batch.batch' => '= ?',
                        'AND',
                        'inventori_batch.barang' => '= ?',
                        'AND',
                        'inventori_batch.deleted_at' => 'IS NULL'
                    ), array(
                        $value['batch'],
                        $targettedObat
                    ))
                    ->execute();

                if(count($checkBatch['response_data']) > 0) {
                    $targettedBatch = $checkBatch['response_data'][0]['uid'];
                } else {
                    if(parent::validateDate($value['kedaluarsa'])) {
                        $targettedBatch = parent::gen_uuid();
                        $newBatch = self::$query->insert('inventori_batch', array(
                            'uid' => $targettedBatch,
                            'barang' => $targettedObat,
                            'po' => $PO,
                            'batch' => $value['batch'],
                            'expired_date' => date('Y-m-d', strtotime($value['kedaluarsa'])),
                            'created_at' => parent::format_date(),
                            'updated_at' => parent::format_date()
                        ))
                            ->execute();
                    }
                }
            }



            $total_all += floatval($value['harga']);

            //PO Detail
            $PODetail = self::$query->insert('inventori_po_detail', array(
                'po' => $PO,
                'barang' => $targettedObat,
                'qty' => floatval($value['stok']),
                'satuan' => $targettedSatuan,
                'harga' => floatval($value['harga']),
                'disc' => 0,
                'disc_type' => 'N',
                'subtotal' => (floatval($value['stok']) * floatval($value['harga'])),
                'keterangan' => 'AUTO PO [STOK AWAL - ' . $UserData['data']->nama . ']',
                'status' => 'L'/*,
                'created_at' => parent::format_date(),
                'updated_at' => parent::format_date()*/
            ))
                ->execute();

            array_push($PODetailResult, $PODetail);

            if(floatval($value['stok']) > 0 || $parameter['gudang'] === __GUDANG_UTAMA__) {
                //Gudang utama perlu stok kosong
                //Di apotek perlu stok yang ada saja



                //Import Stok Obat
                $StokAwal = self::$query->insert('inventori_stok', array(
                    'barang' => $targettedObat,
                    'batch' => $targettedBatch,
                    'gudang' => $parameter['gudang'],
                    'stok_terkini' => floatval($value['stok'])
                ))
                    ->execute();

                if($StokAwal['response_result'] > 0) {
                    $StokLog = self::$query->insert('inventori_stok_log', array(
                        'barang' => $targettedObat,
                        'batch' => $targettedBatch,
                        'gudang' => $parameter['gudang'],
                        'masuk' => floatval($value['stok']),
                        'keluar' => 0,
                        'saldo' => floatval($value['stok']),
                        'type' => __STATUS_STOK_AWAL__,
                        'logged_at' => parent::format_date(),
                        'jenis_transaksi' => 'inventori_batch',
                        'uid_foreign' => $targettedBatch,
                        'keterangan' => 'Stok Awal. Auto PO'
                    ))
                        ->execute();
                }

                array_push($proceed_data, $StokAwal);
            }
        }


        //Auto Purchase List

        $Purchase = self::$query->insert('inventori_po', array(
            'uid' => $PO,
            'pegawai' => $UserData['data']->uid,
            'total' => floatval($total_all),
            'disc' => 0,
            'disc_type' => 'N',
            'total_after_disc' => $total_all,
            'keterangan' => 'AUTO [TIDAK ADA SUPPLIER] - AUTO HARGA JUAL',
            'status' => 'A',
            'nomor_po' => 'STOK_AWAL',
            'tanggal_po' => parent::format_date(),
            'created_at' => parent::format_date(),
            'updated_at' => parent::format_date()
        ))
            ->execute();

        return array(
            'duplicate_row' => $duplicate_row,
            'non_active' => $non_active,
            'success_proceed' => $success_proceed,
            'data' => $all_data,
            'po' => $Purchase,
            'po_detail' => $PODetailResult,
            'proceed' => $proceed_data
        );
    }



























//===========================================================================================KATEGORI
    private function tambah_kategori_obat($parameter)
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);

        $check = self::duplicate_check(array(
            'table' => 'master_inv_obat_kategori',
            'check' => $parameter['nama']
        ));
        if (count($check['response_data']) > 0) {
            $check['response_message'] = 'Duplicate data detected';
            $check['response_result'] = 0;
            unset($check['response_data']);
            return $check;
        } else {
            $uid = parent::gen_uuid();
            $worker = self::$query
                ->insert('master_inv_obat_kategori', array(
                    'uid' => $uid,
                    'nama' => $parameter['nama'],
                    'created_at' => parent::format_date(),
                    'updated_at' => parent::format_date()
                ))
                ->execute();
            if ($worker['response_result'] > 0) {
                $log = parent::log(array(
                    'type' => 'activity',
                    'column' => array(
                        'unique_target',
                        'user_uid',
                        'table_name',
                        'action',
                        'logged_at',
                        'status',
                        'login_id'
                    ),
                    'value' => array(
                        $uid,
                        $UserData['data']->uid,
                        'master_inv_obat_kategori',
                        'I',
                        parent::format_date(),
                        'N',
                        $UserData['data']->log_id
                    ),
                    'class' => __CLASS__
                ));
            }
            return $worker;
        }
    }

    private function edit_kategori_obat($parameter)
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);

        $old = self::get_kategori_obat_detail($parameter['uid']);

        $worker = self::$query
            ->update('master_inv_obat_kategori', array(
                'nama' => $parameter['nama'],
                'updated_at' => parent::format_date()
            ))
            ->where(array(
                'master_inv_obat_kategori.deleted_at' => 'IS NULL',
                'AND',
                'master_inv_obat_kategori.uid' => '= ?'
            ), array(
                $parameter['uid']
            ))
            ->execute();

        if ($worker['response_result'] > 0) {
            unset($parameter['access_token']);


            $log = parent::log(array(
                'type' => 'activity',
                'column' => array(
                    'unique_target',
                    'user_uid',
                    'table_name',
                    'action',
                    'old_value',
                    'new_value',
                    'logged_at',
                    'status',
                    'login_id'
                ),
                'value' => array(
                    $parameter['uid'],
                    $UserData['data']->uid,
                    'master_inv_obat_kategori',
                    'U',
                    json_encode($old['response_data'][0]),
                    json_encode($parameter),
                    parent::format_date(),
                    'N',
                    $UserData['data']->log_id
                ),
                'class' => __CLASS__
            ));
        }

        return $worker;
    }

    private function tambah_kategori($parameter)
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);

        $check = self::duplicate_check(array(
            'table' => 'master_inv_kategori',
            'check' => $parameter['nama']
        ));
        if (count($check['response_data']) > 0) {
            $check['response_message'] = 'Duplicate data detected';
            $check['response_result'] = 0;
            unset($check['response_data']);
            return $check;
        } else {
            $uid = parent::gen_uuid();
            $worker = self::$query
                ->insert('master_inv_kategori', array(
                    'uid' => $uid,
                    'nama' => $parameter['nama'],
                    'created_at' => parent::format_date(),
                    'updated_at' => parent::format_date()
                ))
                ->execute();
            if ($worker['response_result'] > 0) {
                $log = parent::log(array(
                    'type' => 'activity',
                    'column' => array(
                        'unique_target',
                        'user_uid',
                        'table_name',
                        'action',
                        'logged_at',
                        'status',
                        'login_id'
                    ),
                    'value' => array(
                        $uid,
                        $UserData['data']->uid,
                        'master_inv_kategori',
                        'I',
                        parent::format_date(),
                        'N',
                        $UserData['data']->log_id
                    ),
                    'class' => __CLASS__
                ));
            }
            return $worker;
        }
    }

    private function edit_kategori($parameter)
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);

        $old = self::get_kategori_detail($parameter['uid']);

        $worker = self::$query
            ->update('master_inv_kategori', array(
                'nama' => $parameter['nama'],
                'updated_at' => parent::format_date()
            ))
            ->where(array(
                'master_inv_kategori.deleted_at' => 'IS NULL',
                'AND',
                'master_inv_kategori.uid' => '= ?'
            ), array(
                $parameter['uid']
            ))
            ->execute();

        if ($worker['response_result'] > 0) {
            unset($parameter['access_token']);


            $log = parent::log(array(
                'type' => 'activity',
                'column' => array(
                    'unique_target',
                    'user_uid',
                    'table_name',
                    'action',
                    'old_value',
                    'new_value',
                    'logged_at',
                    'status',
                    'login_id'
                ),
                'value' => array(
                    $parameter['uid'],
                    $UserData['data']->uid,
                    'master_inv_kategori',
                    'U',
                    json_encode($old['response_data'][0]),
                    json_encode($parameter),
                    parent::format_date(),
                    'N',
                    $UserData['data']->log_id
                ),
                'class' => __CLASS__
            ));
        }

        return $worker;
    }

    private function get_kategori()
    {
        $data = self::$query
            ->select('master_inv_kategori', array(
                'uid',
                'nama',
                'created_at',
                'updated_at'
            ))
            ->where(array(
                'master_inv_kategori.deleted_at' => 'IS NULL'
            ))
            ->execute();

        $autonum = 1;
        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['autonum'] = $autonum;
            $autonum++;
        }
        return $data;
    }

    private function get_kategori_detail($parameter)
    {
        $data = self::$query
            ->select('master_inv_kategori', array(
                'uid',
                'nama',
                'created_at',
                'updated_at'
            ))
            ->where(array(
                'master_inv_kategori.deleted_at' => 'IS NULL',
                'AND',
                'master_inv_kategori.uid' => '= ?'
            ), array(
                $parameter
            ))
            ->execute();

        $autonum = 1;
        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['autonum'] = $autonum;
            $autonum++;
        }
        return $data;
    }

    private function get_kategori_obat()
    {
        $data = self::$query
            ->select('master_inv_obat_kategori', array(
                'uid',
                'nama',
                'created_at',
                'updated_at'
            ))
            ->where(array(
                'master_inv_obat_kategori.deleted_at' => 'IS NULL'
            ))
            ->execute();

        $autonum = 1;
        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['autonum'] = $autonum;
            $autonum++;
        }
        return $data;
    }

    private function get_kategori_obat_detail($parameter)
    {
        $data = self::$query
            ->select('master_inv_obat_kategori', array(
                'uid',
                'nama',
                'created_at',
                'updated_at'
            ))
            ->where(array(
                'master_inv_obat_kategori.deleted_at' => 'IS NULL',
                'AND',
                'master_inv_obat_kategori.uid' => '= ?'
            ), array(
                $parameter
            ))
            ->execute();

        $autonum = 1;
        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['autonum'] = $autonum;
            $autonum++;
        }
        return $data;
    }

//===========================================================================================SATUAN
    private function get_satuan()
    {
        $data = self::$query
            ->select('master_inv_satuan', array(
                'uid',
                'nama',
                'created_at',
                'updated_at'
            ))
            ->where(array(
                'master_inv_satuan.deleted_at' => 'IS NULL'
            ))
            ->execute();

        $autonum = 1;
        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['autonum'] = $autonum;
            $autonum++;
        }
        return $data;
    }

    public function get_satuan_detail($parameter)
    {
        $data = self::$query
            ->select('master_inv_satuan', array(
                'uid',
                'nama',
                'created_at',
                'updated_at'
            ))
            ->where(array(
                'master_inv_satuan.deleted_at' => 'IS NULL',
                'AND',
                'master_inv_satuan.uid' => '= ?'
            ), array(
                $parameter
            ))
            ->execute();

        $autonum = 1;
        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['autonum'] = $autonum;
            $autonum++;
        }
        return $data;
    }

    private function tambah_satuan($parameter)
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);

        $check = self::duplicate_check(array(
            'table' => 'master_inv_satuan',
            'check' => $parameter['nama']
        ));
        if (count($check['response_data']) > 0) {
            $check['response_message'] = 'Duplicate data detected';
            $check['response_result'] = 0;
            unset($check['response_data']);
            return $check;
        } else {
            $uid = parent::gen_uuid();
            $worker = self::$query
                ->insert('master_inv_satuan', array(
                    'uid' => $uid,
                    'nama' => $parameter['nama'],
                    'created_at' => parent::format_date(),
                    'updated_at' => parent::format_date()
                ))
                ->execute();
            if ($worker['response_result'] > 0) {
                $log = parent::log(array(
                    'type' => 'activity',
                    'column' => array(
                        'unique_target',
                        'user_uid',
                        'table_name',
                        'action',
                        'logged_at',
                        'status',
                        'login_id'
                    ),
                    'value' => array(
                        $uid,
                        $UserData['data']->uid,
                        'master_inv_satuan',
                        'I',
                        parent::format_date(),
                        'N',
                        $UserData['data']->log_id
                    ),
                    'class' => __CLASS__
                ));
            }
            return $worker;
        }
    }

    private function edit_satuan($parameter)
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);

        $old = self::get_satuan_detail($parameter['uid']);

        $worker = self::$query
            ->update('master_inv_satuan', array(
                'nama' => $parameter['nama'],
                'updated_at' => parent::format_date()
            ))
            ->where(array(
                'master_inv_satuan.deleted_at' => 'IS NULL',
                'AND',
                'master_inv_satuan.uid' => '= ?'
            ), array(
                $parameter['uid']
            ))
            ->execute();

        if ($worker['response_result'] > 0) {
            unset($parameter['access_token']);


            $log = parent::log(array(
                'type' => 'activity',
                'column' => array(
                    'unique_target',
                    'user_uid',
                    'table_name',
                    'action',
                    'old_value',
                    'new_value',
                    'logged_at',
                    'status',
                    'login_id'
                ),
                'value' => array(
                    $parameter['uid'],
                    $UserData['data']->uid,
                    'master_inv_satuan',
                    'U',
                    json_encode($old['response_data'][0]),
                    json_encode($parameter),
                    parent::format_date(),
                    'N',
                    $UserData['data']->log_id
                ),
                'class' => __CLASS__
            ));
        }

        return $worker;
    }

//===========================================================================================PENJAMIN
    private function get_penjamin($parameter)
    {
        $data = self::$query->select('master_inv_harga', array(
            'id',
            'barang',
            'penjamin',
            'profit',
            'profit_type'
        ))
            ->where(array(
                'master_inv_harga.deleted_at' => 'IS NULL',
                'AND',
                'master_inv_harga.barang' => '= ?'
            ), array(
                $parameter
            ))
            ->execute();
        return $data['response_data'];
    }

    private function get_rak($parameter)
    {
        $data = self::$query->select('master_inv_gudang_rak', array(
            'id',
            'barang',
            'gudang',
            'rak'
        ))
            ->where(array(
                'master_inv_gudang_rak.deleted_at' => 'IS NULL',
                'AND',
                'master_inv_gudang_rak.barang' => '= ?'
            ), array(
                $parameter
            ))
            ->execute();
        return $data['response_data'];
    }

    private function get_monitoring($parameter)
    {
        $data = self::$query->select('master_inv_monitoring', array(
            'id',
            'barang',
            'gudang',
            'min',
            'max'
        ))
            ->where(array(
                'master_inv_monitoring.deleted_at' => 'IS NULL',
                'AND',
                'master_inv_monitoring.barang' => '= ?'
            ), array(
                $parameter
            ))
            ->execute();
        return $data['response_data'];
    }

    private function get_kategori_obat_item($parameter)
    {
        $data = self::$query->select('master_inv_obat_kategori_item', array(
            'id',
            'obat',
            'kategori'
        ))
            ->where(array(
                'master_inv_obat_kategori_item.deleted_at' => 'IS NULL',
                'AND',
                'master_inv_obat_kategori_item.obat' => '= ?'
            ), array(
                $parameter
            ))
            ->execute();
        return $data['response_data'];
    }

    private function get_kategori_obat_item_parsed($parameter)
    {
        $data = self::$query->select('master_inv_obat_kategori_item', array(
            'id',
            'obat',
            'kategori'
        ))
            ->where(array(
                'master_inv_obat_kategori_item.deleted_at' => 'IS NULL',
                'AND',
                'master_inv_obat_kategori_item.obat' => '= ?'
            ), array(
                $parameter
            ))
            ->execute();
        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['kategori'] = self::get_kategori_obat_detail($value['kategori'])['response_data'][0];
        }
        return $data['response_data'];
    }

//===========================================================================================GUDANG
    private function get_gudang()
    {
        $data = self::$query
            ->select('master_inv_gudang', array(
                'uid',
                'nama',
                'created_at',
                'updated_at'
            ))
            ->where(array(
                'master_inv_gudang.deleted_at' => 'IS NULL'
            ))
            ->execute();

        $autonum = 1;
        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['autonum'] = $autonum;
            $autonum++;
        }
        return $data;
    }

    public function get_gudang_detail($parameter)
    {
        $data = self::$query
            ->select('master_gudang', array(
                'uid',
                'jenis_gudang',
                'foreign_data',
                'created_at',
                'updated_at'
            ))
            ->where(array(
                'master_gudang.deleted_at' => 'IS NULL',
                'AND',
                'master_gudang.foreign_data' => '= ?'
            ), array(
                $parameter
            ))
            ->execute();

        $autonum = 1;
        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['autonum'] = $autonum;
            $autonum++;
        }
        return $data;
    }

    private function tambah_gudang($parameter)
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);

        $check = self::duplicate_check(array(
            'table' => 'master_inv_gudang',
            'check' => $parameter['nama']
        ));
        if (count($check['response_data']) > 0) {
            $check['response_message'] = 'Duplicate data detected';
            $check['response_result'] = 0;
            unset($check['response_data']);
            return $check;
        } else {
            $uid = parent::gen_uuid();
            $worker = self::$query
                ->insert('master_inv_gudang', array(
                    'uid' => $uid,
                    'nama' => $parameter['nama'],
                    'created_at' => parent::format_date(),
                    'updated_at' => parent::format_date()
                ))
                ->execute();
            if ($worker['response_result'] > 0) {
                $log = parent::log(array(
                    'type' => 'activity',
                    'column' => array(
                        'unique_target',
                        'user_uid',
                        'table_name',
                        'action',
                        'logged_at',
                        'status',
                        'login_id'
                    ),
                    'value' => array(
                        $uid,
                        $UserData['data']->uid,
                        'master_inv_gudang',
                        'I',
                        parent::format_date(),
                        'N',
                        $UserData['data']->log_id
                    ),
                    'class' => __CLASS__
                ));
            }
            return $worker;
        }
    }

    private function edit_gudang($parameter)
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);

        $old = self::get_gudang_detail($parameter['uid']);

        $worker = self::$query
            ->update('master_inv_gudang', array(
                'nama' => $parameter['nama'],
                'updated_at' => parent::format_date()
            ))
            ->where(array(
                'master_inv_gudang.deleted_at' => 'IS NULL',
                'AND',
                'master_inv_gudang.uid' => '= ?'
            ), array(
                $parameter['uid']
            ))
            ->execute();

        if ($worker['response_result'] > 0) {
            unset($parameter['access_token']);


            $log = parent::log(array(
                'type' => 'activity',
                'column' => array(
                    'unique_target',
                    'user_uid',
                    'table_name',
                    'action',
                    'old_value',
                    'new_value',
                    'logged_at',
                    'status',
                    'login_id'
                ),
                'value' => array(
                    $parameter['uid'],
                    $UserData['data']->uid,
                    'master_inv_gudang',
                    'U',
                    json_encode($old['response_data'][0]),
                    json_encode($parameter),
                    parent::format_date(),
                    'N',
                    $UserData['data']->log_id
                ),
                'class' => __CLASS__
            ));
        }

        return $worker;
    }

//===========================================================================================Manufacture
    private function get_manufacture()
    {
        $data = self::$query
            ->select('master_manufacture', array(
                'uid',
                'nama',
                'created_at',
                'updated_at'
            ))
            ->where(array(
                'master_manufacture.deleted_at' => 'IS NULL'
            ))
            ->execute();

        $autonum = 1;
        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['autonum'] = $autonum;
            $autonum++;
        }
        return $data;
    }

    private function get_manufacture_detail($parameter)
    {
        $data = self::$query
            ->select('master_manufacture', array(
                'uid',
                'nama',
                'created_at',
                'updated_at'
            ))
            ->where(array(
                'master_manufacture.deleted_at' => 'IS NULL',
                'AND',
                'master_manufacture.uid' => '= ?'
            ), array(
                $parameter
            ))
            ->execute();

        $autonum = 1;
        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['autonum'] = $autonum;
            $autonum++;
        }
        return $data;
    }

    private function tambah_manufacture($parameter)
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);

        $check = self::duplicate_check(array(
            'table' => 'master_manufacture',
            'check' => $parameter['nama']
        ));
        if (count($check['response_data']) > 0) {
            $check['response_message'] = 'Duplicate data detected';
            $check['response_result'] = 0;
            unset($check['response_data']);
            return $check;
        } else {
            $uid = parent::gen_uuid();
            $worker = self::$query
                ->insert('master_manufacture', array(
                    'uid' => $uid,
                    'nama' => $parameter['nama'],
                    'created_at' => parent::format_date(),
                    'updated_at' => parent::format_date()
                ))
                ->execute();
            if ($worker['response_result'] > 0) {
                $log = parent::log(array(
                    'type' => 'activity',
                    'column' => array(
                        'unique_target',
                        'user_uid',
                        'table_name',
                        'action',
                        'logged_at',
                        'status',
                        'login_id'
                    ),
                    'value' => array(
                        $uid,
                        $UserData['data']->uid,
                        'master_manufacture',
                        'I',
                        parent::format_date(),
                        'N',
                        $UserData['data']->log_id
                    ),
                    'class' => __CLASS__
                ));
            }
            return $worker;
        }
    }

    private function edit_manufacture($parameter)
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);

        $old = self::get_manufacture_detail($parameter['uid']);

        $worker = self::$query
            ->update('master_manufacture', array(
                'nama' => $parameter['nama'],
                'updated_at' => parent::format_date()
            ))
            ->where(array(
                'master_manufacture.deleted_at' => 'IS NULL',
                'AND',
                'master_manufacture.uid' => '= ?'
            ), array(
                $parameter['uid']
            ))
            ->execute();

        if ($worker['response_result'] > 0) {
            unset($parameter['access_token']);


            $log = parent::log(array(
                'type' => 'activity',
                'column' => array(
                    'unique_target',
                    'user_uid',
                    'table_name',
                    'action',
                    'old_value',
                    'new_value',
                    'logged_at',
                    'status',
                    'login_id'
                ),
                'value' => array(
                    $parameter['uid'],
                    $UserData['data']->uid,
                    'master_manufacture',
                    'U',
                    json_encode($old['response_data'][0]),
                    json_encode($parameter),
                    parent::format_date(),
                    'N',
                    $UserData['data']->log_id
                ),
                'class' => __CLASS__
            ));
        }

        return $worker;
    }

//===========================================================================================ITEM DETAIL
    private function get_konversi($parameter)
    {
        $dataKonversi = self::$query
            ->select('master_inv_satuan_konversi', array(
                'dari_satuan',
                'ke_satuan',
                'rasio'
            ))
            ->where(array(
                'master_inv_satuan_konversi.barang' => '= ?'
            ), array(
                $parameter
            ))
            ->execute();
        return $dataKonversi['response_data'];
    }

    private function get_varian($parameter)
    { //array('barang'=>'', 'satuan' => '')
        $dataVarian = self::$query
            ->select('master_inv_satuan_varian', array(
                'satuan',
                'nama'
            ))
            ->where(array(
                'master_inv_satuan_varian.barang' => '= ?'
            ), array(
                $parameter
            ))
            ->execute();
        return $dataVarian['response_data'];
    }

    private function get_kombinasi($parameter)
    { //array('barang'=>'', 'satuan' => '')
        $dataVarian = self::$query
            ->select('master_inv_kombinasi', array(
                'barang_kombinasi',
                'satuan',
                'varian',
                'qty'
            ))
            ->where(array(
                'master_inv_kombinasi.barang' => '= ?'
            ), array(
                $parameter
            ))
            ->execute();
        return $dataVarian['response_data'];
    }

//===========================================================================================ITEM


    private function get_item_select2($parameter)
    {
        $data = self::$query
            ->select('master_inv', array(
                'uid',
                'kode_barang',
                'nama',
                'kategori',
                'satuan_terkecil',
                'created_at',
                'updated_at'
            ))
            ->where(array(
                'master_inv.deleted_at' => 'IS NULL',
                'AND',
                '(master_inv.kode_barang' => 'ILIKE ' . '\'%' . $_GET['search'] . '%\'',
                'OR',
                'master_inv.nama' => 'ILIKE ' . '\'%' . $_GET['search'] . '%\')'
            ))
            ->limit(10)
            ->execute();

        $autonum = 1;
        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['autonum'] = $autonum;
            $data['response_data'][$key]['satuan_terkecil'] = self::get_satuan_detail($value['satuan_terkecil'])['response_data'][0];
            $data['response_data'][$key]['kategori'] = self::get_kategori_detail($value['kategori'])['response_data'][0];


            $dataHarga = self::$query->select('strategi_penjualan', array(
                'id', 'produk', 'tanggal',
                'member_cashback', 'member_royalti', 'member_reward', 'member_insentif_personal',
                'stokis_cashback', 'stokis_royalti', 'stokis_reward', 'stokis_insentif_personal',
                'harga_jual_member',
                'discount_type_member',
                'discount_member',
                'harga_akhir_member',

                'harga_jual_stokis',
                'discount_type_stokis',
                'discount_stokis',
                'harga_akhir_stokis'
            ))
                ->where(array(
                    'strategi_penjualan.produk' => '= ?',
                    'AND',
                    'strategi_penjualan.tanggal' => '= ?',
                    'AND',
                    'strategi_penjualan.deleted_at' => 'IS NULL'
                ), array(
                    $value['uid'],
                    date('Y-m-d')
                ))
                ->execute();
            $data['response_data'][$key]['harga'] = (count($dataHarga['response_data']) > 0) ? $dataHarga['response_data'][0] : array(
                'member_cashback' => 0,
                'member_royalti' => 0,
                'member_reward' => 0,
                'member_insentif_personal' => 0,
                'harga_akhir_member' => 0,

                'stokis_cashback' => 0,
                'stokis_royalti' => 0,
                'stokis_reward' => 0,
                'stokis_insentif_personal' => 0,
                'harga_akhir_stokis' => 0
            );

            $autonum++;
        }
        return $data;
    }


    private function get_item()
    {
        $data = self::$query
            ->select('master_inv', array(
                'uid',
                'kode_barang',
                'nama',
                'kategori',
                'satuan_terkecil',
                'manufacture',
                'created_at',
                'updated_at'
            ))
            ->where(array(
                'master_inv.deleted_at' => 'IS NULL'
            ))
            ->execute();

        $autonum = 1;
        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['autonum'] = $autonum;
            $kategori_obat = self::get_kategori_obat_item($value['uid']);
            foreach ($kategori_obat as $KOKey => $KOValue) {
                $KategoriCaption = self::get_kategori_obat_detail($KOValue['kategori'])['response_data'][0]['nama'];
                if(!empty($KategoriCaption)) {
                    $kategori_obat[$KOKey]['kategori'] = $KategoriCaption;
                }
            }

            $data['response_data'][$key]['kategori_obat'] = $kategori_obat;
            $data['response_data'][$key]['satuan_terkecil'] = self::get_satuan_detail($value['satuan_terkecil'])['response_data'][0];
            $data['response_data'][$key]['kategori'] = self::get_kategori_detail($value['kategori'])['response_data'][0];
            $data['response_data'][$key]['manufacture'] = self::get_manufacture_detail($value['manufacture'])['response_data'][0];

            //Data Penjamin
            $PenjaminObat = new Penjamin(self::$pdo);
            $ListPenjaminObat = $PenjaminObat::get_penjamin_obat($value['uid'])['response_data'];
            foreach ($ListPenjaminObat as $PenjaminKey => $PenjaminValue) {
                $ListPenjaminObat[$PenjaminKey]['profit'] = floatval($PenjaminValue['profit']);
            }
            $data['response_data'][$key]['penjamin'] = $ListPenjaminObat;

            //Cek Ketersediaan Stok
            $TotalStock = 0;
            $InventoriStockPopulator = self::get_item_batch($value['uid']);
            if (count($InventoriStockPopulator['response_data']) > 0) {
                foreach ($InventoriStockPopulator['response_data'] as $TotalKey => $TotalValue) {
                    $TotalStock += floatval($TotalValue['stok_terkini']);
                }
                $data['response_data'][$key]['stok'] = $TotalStock;
                $data['response_data'][$key]['batch'] = $InventoriStockPopulator['response_data'];
            } else {
                $data['response_data'][$key]['stok'] = 0;
            }

            $autonum++;
        }
        return $data;
    }

    public function get_item_batch($parameter)
    {
        $data = self::$query->select('inventori_stok', array(
            'batch',
            'barang',
            'gudang',
            'stok_terkini'
        ))
            ->where(array(
                'inventori_stok.barang' => '= ?'
            ), array(
                $parameter
            ))
            ->order(array(
                'gudang' => 'DESC'
            ))
            ->execute();
        foreach ($data['response_data'] as $key => $value) {
            $batch_info = self::get_batch_detail($value['batch'])['response_data'][0];

            if (
                $batch_info['expired_date'] < date('Y-m-d') ||
                floatval($value['stok_terkini']) < 0
            ) { //Expired jangan dijual
                unset($data['response_data'][$key]);
            } else {
                //$data['response_data'][$key]['item_detail'] = self::get_item_detail($value['barang'])['response_data'][0];
                $data['response_data'][$key]['gudang'] = self::get_gudang_detail($value['gudang'])['response_data'][0];
                $data['response_data'][$key]['kode'] = self::get_batch_detail($value['batch'])['response_data'][0]['batch'];
                $data['response_data'][$key]['expired'] = date('d F Y', strtotime($batch_info['expired_date']));
                $data['response_data'][$key]['stok_terkini'] = floatval($value['stok_terkini']);
                $data['response_data'][$key]['expired_sort'] = $batch_info['expired_date'];
                $data['response_data'][$key]['harga'] = $batch_info['harga'];
                $data['response_data'][$key]['profit'] = $batch_info['profit'];
            }
        }

        //Sort Batch before return
        /*$sorted = $data['response_data'];
        array_multisort($sorted, SORT_ASC, $data['response_data']);
        $data['response_data'] = $sorted;*/
        usort($data['response_data'], function ($a, $b)
        {
            $t1 = strtotime($a['expired_sort']);
            $t2 = strtotime($b['expired_sort']);
            return $t1 - $t2;
        });
        return $data;
    }

    public function get_batch_detail($parameter)
    {
        $data = self::$query->select('inventori_batch', array(
            'uid',
            'batch',
            'barang',
            'expired_date',
            'po',
            'do_master'
        ))
            ->where(array(
                'inventori_batch.uid' => ' = ?'
            ), array(
                $parameter
            ))
            ->execute();
        foreach ($data['response_data'] as $key => $value) {
            //Get Harga dari PO
            if (isset($value['po'])) {
                $PO = new PO(self::$pdo);
                $Price = $PO->get_po_item_price(array(
                    'po' => $value['po'],
                    'barang' => $value['barang']
                ));

                $data['response_data'][$key]['harga'] = floatval($Price['response_data'][0]['harga']);

                //Tambahkan Keuntungan yang diinginkan dari master inventori
                $Profit = self::get_penjamin($value['barang']);
                $data['response_data'][$key]['profit'] = $Profit;
            } else {
                $data['response_data'][$key]['harga'] = 0;
            }
        }

        return $data;
    }


    /**
     * @param uid $parameter Barang
     * @param uid $gudang Gudang
     * @return array Data is response_data
     */
    public function  get_item_stok_log($parameter, $gudang, $dari = "", $sampai = "") {
        if($dari !== "" && $sampai !== "") {
            $data = self::$query->select('inventori_stok_log', array(
                'id',
                'masuk',
                'keluar',
                'saldo',
                'batch',
                'type',
                'logged_at',
                'jenis_transaksi',
                'uid_foreign',
                'keterangan'
            ))
                ->where(array(
                    'inventori_stok_log.gudang' => '= ?',
                    'AND',
                    'inventori_stok_log.barang' => '= ?',
                    'AND',
                    'inventori_stok_log.logged_at' => 'BETWEEN ? AND ?'
                ), array(
                    $gudang,
                    $parameter,
                    date('Y-m-d', strtotime($dari)),
                    date('Y-m-d', strtotime($sampai))
                ))
                ->execute();
        } else {
            $data = self::$query->select('inventori_stok_log', array(
                'id',
                'masuk',
                'keluar',
                'saldo',
                'batch',
                'type',
                'logged_at',
                'jenis_transaksi',
                'uid_foreign',
                'keterangan'
            ))
                ->where(array(
                    'inventori_stok_log.gudang' => '= ?',
                    'AND',
                    'inventori_stok_log.barang' => '= ?'
                ), array(
                    $gudang,
                    $parameter
                ))
                ->execute();
        }

        $DO = new DeliveryOrder(self::$pdo);

        foreach ($data['response_data'] as $key => $value) {
            //Terminologi Item
            $Termi = self::$query->select('terminologi_item', array(
                'id',
                'nama'
            ))
                ->where(array(
                    'terminologi_item.id' => '= ?',
                    'AND',
                    'terminologi_item.deleted_at' => 'IS NULL'
                ), array(
                    $value['type']
                ))
                ->execute();
            $data['response_data'][$key]['type'] = $Termi['response_data'][0];

            $data['response_data'][$key]['batch'] = self::get_batch_detail($value['batch'])['response_data'][0];

            $data['response_data'][$key]['logged_at'] = date('d M Y', strtotime($value['logged_at']));

            if($value['uid_foreign'] !== null && $value['uid_foreign'] !== '')
            {
                //Get Foreign dokumen
                if($value['jenis_transaksi'] === 'resep') {
                    //get resep
                    $Resep = self::$query->select('resep', array(
                        'pasien'
                    ))
                        ->where(array(
                            'resep.deleted_at' => 'IS NULL',
                            'AND',
                            'resep.uid' => '= ?'
                        ), array(
                            $value['uid_foreign']
                        ))
                        ->execute();
                    //get pasien
                    $Pasien = new Pasien(self::$pdo);
                    $PasienInfo = $Pasien::get_pasien_detail('pasien', $Resep['response_data'][0]['pasien']);

                    $data['response_data'][$key]['dokumen'] = 'Resep Asesmen ' . $PasienInfo['response_data'][0]['nama'];
                } elseif ($value['jenis_transaksi'] === 'inventori_do') {
                    $DODetail = $DO->get_do_info($value['uid_foreign'])['response_data'][0];
                    $data['response_data'][$key]['dokumen'] = $DODetail['no_do'];
                } else {
                    $data['response_data'][$key]['dokumen'] = '-';
                }
            } else {
                $data['response_data'][$key]['dokumen'] = '-';
            }
        }

        return $data;
    }

    /**
     * @param $parameter Require item uid
     * @return array
     */
    public function get_item_detail($parameter, $Old = false)
    {
        $data = self::$query
            ->select('master_inv', array(
                'uid',
                'nama',
                'het',
                'kode_barang',
                'keterangan',
                'kategori',
                'satuan_terkecil',
                'created_at',
                'updated_at'
            ))
            ->where(array(
                'master_inv.deleted_at' => 'IS NULL',
                'AND',
                'master_inv.uid' => '= ?'
            ), array(
                $parameter
            ))
            ->execute();

        $autonum = 1;
        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['autonum'] = $autonum;
            $autonum++;

            //Text
            $data['response_data'][$key]['id'] = $value['uid'];
            $data['response_data'][$key]['text'] = strtoupper($value['nama']);

            $data['response_data'][$key]['nama'] = strtoupper($value['nama']);

            $data['response_data'][$key]['kode_barang'] = strtoupper($value['kode_barang']);
            if(file_exists('../images/produk/' . $value['uid'] . '.png')) {
                $data['response_data'][$key]['url_gambar'] = array(__HOST__ . 'images/produk/' . $value['uid'] . '.png');
            } else {
                $data['response_data'][$key]['url_gambar'] = array(__HOST__ . 'images/product.png');
            }

            //Satuan Terkecil
            $data['response_data'][$key]['satuan_terkecil_nama'] = self::get_satuan_detail($value['satuan_terkecil'])['response_data'][0]['nama'];


            //Paket
            $getPaket = self::$query->select('master_inv_paket', array(
                'id', 'barang', 'qty'
            ))
                ->where(array(
                    'master_inv_paket.deleted_at' => 'IS NULL',
                    'AND',
                    'master_inv_paket.parent_barang' => '= ?'
                ), array(
                    $value['uid']
                ))
                ->execute();

            foreach ($getPaket['response_data'] as $pakKey => $pakValue) {
                $getPaket['response_data'][$pakKey]['barang'] = self::get_item_detail($pakValue['barang'])['response_data'][0];
            }

            $data['response_data'][$key]['paket'] = $getPaket['response_data'];

            $hargaItem = self::$query->select('master_inv_harga', array(
                'id',
                'produk',
                'member_type',
                'id_table',
                'het',
                'discount_type',
                'discount',
                'jual',
                'cashback',
                'reward',
                'royalti',
                'insentif',
                'created_at',
                'updated_at'
            ))
                ->limit(2)
                ->where(array(
                    'master_inv_harga.produk' => '= ?',
                    'AND',
                    'master_inv_harga.deleted_at' => 'IS NULL'
                ), array(
                    $value['uid']
                ))
                ->order(array(
                    'master_inv_harga.created_at' => 'ASC'
                ))
                ->execute();
            foreach ($hargaItem['response_data'] as $pointKey => $pointValue) {
                $hargaItem['response_data'][$pointKey]['tanggal'] = date('d F Y', strtotime($pointValue['created_at']));
                $hargaItem['response_data'][$pointKey]['het'] = (isset($pointValue['het']) ? floatval($pointValue['het']) : 0);
                $hargaItem['response_data'][$pointKey]['discount'] = (isset($pointValue['discount']) ? floatval($pointValue['discount']) : 0);
                $hargaItem['response_data'][$pointKey]['jual'] = floatval($pointValue['jual']);

                $hargaItem['response_data'][$pointKey]['cashback'] = (isset($pointValue['cashback']) ? floatval($pointValue['cashback']) : 0);
                $hargaItem['response_data'][$pointKey]['reward'] = (isset($pointValue['reward']) ? floatval($pointValue['reward']) : 0);
                $hargaItem['response_data'][$pointKey]['royalti'] = (isset($pointValue['royalti']) ? floatval($pointValue['royalti']) : 0);
                $hargaItem['response_data'][$pointKey]['insentif'] = (isset($pointValue['insentif']) ? floatval($pointValue['insentif']) : 0);
            }

            $data['response_data'][$key]['point'] = (count($hargaItem['response_data']) > 0) ? $hargaItem['response_data'] : array(
                'jual' =>  0,
                'discount_type' =>  'N',
                'discount' =>  0,
                'cashback' =>  0,
                'reward' =>  0,
                'royalti' =>  0,
                'insentif' =>  0
            );

            $data['response_data'][$key]['het'] = floatval($value['het']);



            $proc_end = array();
            foreach ($hargaItem['response_data'] as $harga_set_key => $harga_set_value) {
                if(!isset($proc_end['member_cashback'])) {
                    $proc_end['member_cashback'] = 0;
                    $proc_end['member_reward'] = 0;
                    $proc_end['member_royalti'] = 0;
                    $proc_end['member_insentif_personal'] = 0;
                    $proc_end['harga_akhir_member'] = 0;

                    $proc_end['stokis_cashback'] = 0;
                    $proc_end['stokis_reward'] = 0;
                    $proc_end['stokis_royalti'] = 0;
                    $proc_end['stokis_insentif_personal'] = 0;
                    $proc_end['harga_akhir_stokis'] = 0;
                }

                if($harga_set_value['member_type'] === 'M') {
                    $proc_end['member_cashback'] = $harga_set_value['cashback'];
                    $proc_end['member_reward'] = $harga_set_value['reward'];
                    $proc_end['member_royalti'] = $harga_set_value['royalti'];
                    $proc_end['member_insentif_personal'] = $harga_set_value['insentif'];
                    $proc_end['harga_akhir_member'] = $harga_set_value['jual'];
                } else {
                    $proc_end['stokis_cashback'] = $harga_set_value['cashback'];
                    $proc_end['stokis_reward'] = $harga_set_value['reward'];
                    $proc_end['stokis_royalti'] = $harga_set_value['royalti'];
                    $proc_end['stokis_insentif_personal'] = $harga_set_value['insentif'];
                    $proc_end['harga_akhir_stokis'] = $harga_set_value['jual'];
                }

            }

            /*$dataHarga = self::$query->select('strategi_penjualan', array(
                'id', 'produk', 'tanggal',
                'member_cashback', 'member_royalti', 'member_reward', 'member_insentif_personal',
                'stokis_cashback', 'stokis_royalti', 'stokis_reward', 'stokis_insentif_personal',
                'harga_jual_member',
                'discount_type_member',
                'discount_member',
                'harga_akhir_member',

                'harga_jual_stokis',
                'discount_type_stokis',
                'discount_stokis',
                'harga_akhir_stokis'
            ))
                ->where(array(
                    'strategi_penjualan.produk' => '= ?',
                    'AND',
                    'strategi_penjualan.tanggal' => '= ?',
                    'AND',
                    'strategi_penjualan.deleted_at' => 'IS NULL'
                ), array(
                    $parameter,
                    date('Y-m-d')
                ))
                ->execute();

            $data['response_data'][$key]['harga'] = (count($dataHarga['response_data']) > 0) ? $dataHarga['response_data'][0] : array(
                'member_cashback' => 0,
                'member_royalti' => 0,
                'member_reward' => 0,
                'member_insentif_personal' => 0,
                'harga_akhir_member' => 0,

                'stokis_cashback' => 0,
                'stokis_royalti' => 0,
                'stokis_reward' => 0,
                'stokis_insentif_personal' => 0,
                'harga_akhir_stokis' => 0
            );*/

            $data['response_data'][$key]['harga'] = $proc_end;
        }
        $data['response_data'] = $data['response_data'][0];
        return $data;
    }

    public function get_kandungan($parameter) {
        $data = self::$query->select('master_inv_obat_kandungan', array(
            'id', 'kandungan', 'keterangan'
        ))
            ->where(array(
                'master_inv_obat_kandungan.uid_obat' => '= ?',
                'AND',
                'master_inv_obat_kandungan.deleted_at' => 'IS NULL'
            ), array(
                $parameter
            ))
            ->execute();

        return $data;
    }

    private function tambah_item($parameter)
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);
        $error_count = 0;

        //Parent Segment
        $check = self::duplicate_check(array(
            'table' => 'master_inv',
            'check' => $parameter['nama']
        ));
        if (count($check['response_data']) > 0) {
            $check['response_message'] = 'Duplicate data detected';
            $check['response_result'] = 0;
            unset($check['response_data']);
            return $check;
        } else {
            $uid = parent::gen_uuid();
            $worker = self::$query->insert('master_inv', array(
                'uid' => $uid,
                'kode_barang' => $parameter['kode'],
                'het' => floatval($parameter['het']),
                'nama' => $parameter['nama'],
                'kategori' => $parameter['kategori'],
                'satuan_terkecil' => $parameter['satuan_terkecil'],
                'keterangan' => $parameter['keterangan'],
                'created_at' => parent::format_date(),
                'updated_at' => parent::format_date()
            ))
                ->execute();
            if ($worker['response_result'] > 0) {
                $log = parent::log(array(
                    'type' => 'activity',
                    'column' => array(
                        'unique_target',
                        'user_uid',
                        'table_name',
                        'action',
                        'new_value',
                        'logged_at',
                        'status',
                        'login_id'
                    ),
                    'value' => array(
                        $parameter['uid'],
                        $UserData['data']->uid,
                        'master_inv',
                        'I',
                        json_encode($parameter),
                        parent::format_date(),
                        'N',
                        $UserData['data']->log_id
                    ),
                    'class' => __CLASS__
                ));

                //Image Upload
                $data = $parameter['image'];
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);
                if (!file_exists('../images/produk')) {
                    mkdir('../images/produk');
                }
                file_put_contents('../images/produk/' . $uid . '.png', $data);



                //Kategori Obat
                $oldKategoriObat = self::$query->select('master_inv_obat_kategori_item', array(
                    'id',
                    'kategori'
                ))
                    ->where(array(
                        'master_inv_obat_kategori_item.obat' => '= ?'
                    ), array(
                        $uid
                    ))
                    ->execute();

                //Delete unused kategori
                foreach ($oldKategoriObat['response_data'] as $key => $value) {
                    if (!in_array($value['kategori'], $parameter['listKategoriObat'])) {
                        $deleteKategoriObat = self::$query->update('master_inv_obat_kategori_item', array(
                            'deleted_at' => parent::format_date()
                        ))
                            ->where(array(
                                'master_inv_obat_kategori_item.id' => '= ?'
                            ), array(
                                $value['id']
                            ))
                            ->execute();
                        if ($deleteKategoriObat['response_result'] > 0) {
                            //
                        } else {
                            $error_count++;
                        }
                    }
                }


                foreach ($parameter['listKategoriObat'] as $key => $value) {
                    //Check existing
                    $checkKategoriObat = self::$query->select('master_inv_obat_kategori_item', array(
                        'id'
                    ))
                        ->where(array(
                            'obat' => '= ?',
                            'AND',
                            'kategori' => '= ?'
                        ), array(
                            $uid,
                            $value
                        ))
                        ->execute();
                    if (count($checkKategoriObat['response_data']) > 0) {
                        $kategoriObat = self::$query->update('master_inv_obat_kategori_item', array(
                            'deleted_at' => NULL,
                            'updated_at' => parent::format_date()
                        ))
                            ->where(array(
                                'master_inv_obat_kategori_item.id' => '= ?'
                            ), array(
                                $checkKategoriObat['response_data'][0]['id']
                            ))
                            ->execute();
                        if ($kategoriObat['response_result'] > 0) {
                            $log = parent::log(array(
                                'type' => 'activity',
                                'column' => array(
                                    'unique_target',
                                    'user_uid',
                                    'table_name',
                                    'action',
                                    'new_value',
                                    'logged_at',
                                    'status',
                                    'login_id'
                                ),
                                'value' => array(
                                    $parameter['uid'],
                                    $UserData['data']->uid,
                                    'master_inv_obat_kategori_item',
                                    'U',
                                    'activated',
                                    parent::format_date(),
                                    'N',
                                    $UserData['data']->log_id
                                ),
                                'class' => __CLASS__
                            ));
                        } else {
                            $error_count++;
                        }
                    } else {
                        $kategoriObat = self::$query->insert('master_inv_obat_kategori_item', array(
                            'obat' => $uid,
                            'kategori' => $value,
                            'created_at' => parent::format_date(),
                            'updated_at' => parent::format_date()
                        ))
                            ->execute();
                        if ($kategoriObat['response_result'] > 0) {
                            $log = parent::log(array(
                                'type' => 'activity',
                                'column' => array(
                                    'unique_target',
                                    'user_uid',
                                    'table_name',
                                    'action',
                                    'new_value',
                                    'logged_at',
                                    'status',
                                    'login_id'
                                ),
                                'value' => array(
                                    $parameter['uid'],
                                    $UserData['data']->uid,
                                    'master_inv_obat_kategori_item',
                                    'I',
                                    json_encode($parameter['listKategoriObat']),
                                    parent::format_date(),
                                    'N',
                                    $UserData['data']->log_id
                                ),
                                'class' => __CLASS__
                            ));
                        } else {
                            $error_count++;
                        }
                    }
                }


                //Satuan Konversi
                foreach ($parameter['satuanKonversi'] as $key => $value) {
                    $newKonversi = self::$query->insert('master_inv_satuan_konversi', array(
                        'barang' => $uid,
                        'dari_satuan' => $value['dari'],
                        'rasio' => $value['rasio'],
                        'ke_satuan' => $value['ke'],
                        'created_at' => parent::format_date(),
                        'updated_at' => parent::format_date()
                    ))
                        ->execute();
                    if ($newKonversi['response_result'] > 0) {
                        $log = parent::log(array(
                            'type' => 'activity',
                            'column' => array(
                                'unique_target',
                                'user_uid',
                                'table_name',
                                'action',
                                'new_value',
                                'logged_at',
                                'status',
                                'login_id'
                            ),
                            'value' => array(
                                $parameter['uid'],
                                $UserData['data']->uid,
                                'master_inv_satuan_konversi',
                                'I',
                                json_encode($parameter['satuanKonversi']),
                                parent::format_date(),
                                'N',
                                $UserData['data']->log_id
                            ),
                            'class' => __CLASS__
                        ));
                    } else {
                        $error_count++;
                    }
                }

                //Penjamin
                foreach ($parameter['penjaminList'] as $key => $value) {
                    $newPenjamin = self::$query->insert('master_inv_harga', array(
                        'barang' => $uid,
                        'penjamin' => $value['penjamin'],
                        'profit' => $value['marginValue'],
                        'profit_type' => $value['marginType'],
                        'created_at' => parent::format_date(),
                        'updated_at' => parent::format_date()
                    ))
                        ->execute();
                    if (count($newPenjamin['response_result']) > 0) {
                        $log = parent::log(array(
                            'type' => 'activity',
                            'column' => array(
                                'unique_target',
                                'user_uid',
                                'table_name',
                                'action',
                                'new_value',
                                'logged_at',
                                'status',
                                'login_id'
                            ),
                            'value' => array(
                                $parameter['uid'],
                                $UserData['data']->uid,
                                'master_inv_harga',
                                'I',
                                json_encode($parameter['penjaminList']),
                                parent::format_date(),
                                'N',
                                $UserData['data']->log_id
                            ),
                            'class' => __CLASS__
                        ));
                    } else {
                        $error_count++;
                    }
                }

                //Gudang Rak
                foreach ($parameter['gudangMeta'] as $key => $value) {
                    $newGudang = self::$query->insert('master_inv_gudang_rak', array(
                        'barang' => $uid,
                        'gudang' => $value['gudang'],
                        'rak' => $value['lokasi'],
                        'created_at' => parent::format_date(),
                        'updated_at' => parent::format_date()
                    ))
                        ->execute();
                    if ($newGudang['response_result'] > 0) {
                        $log = parent::log(array(
                            'type' => 'activity',
                            'column' => array(
                                'unique_target',
                                'user_uid',
                                'table_name',
                                'action',
                                'new_value',
                                'logged_at',
                                'status',
                                'login_id'
                            ),
                            'value' => array(
                                $parameter['uid'],
                                $UserData['data']->uid,
                                'master_inv_gudang_rak',
                                'I',
                                json_encode($parameter['gudangMeta']),
                                parent::format_date(),
                                'N',
                                $UserData['data']->log_id
                            ),
                            'class' => __CLASS__
                        ));
                    } else {
                        $error_count++;
                    }
                }

                //Monitoring
                foreach ($parameter['monitoring'] as $key => $value) {
                    $newMonitoring = self::$query->insert('master_inv_monitoring', array(
                        'barang' => $uid,
                        'gudang' => $value['gudang'],
                        'min' => $value['min'],
                        'max' => $value['max'],
                        'created_at' => parent::format_date(),
                        'updated_at' => parent::format_date()
                    ))
                        ->execute();
                    if ($newMonitoring['response_result'] > 0) {
                        $log = parent::log(array(
                            'type' => 'activity',
                            'column' => array(
                                'unique_target',
                                'user_uid',
                                'table_name',
                                'action',
                                'new_value',
                                'logged_at',
                                'status',
                                'login_id'
                            ),
                            'value' => array(
                                $parameter['uid'],
                                $UserData['data']->uid,
                                'master_inv_monitoring',
                                'I',
                                json_encode($parameter['monitoring']),
                                parent::format_date(),
                                'N',
                                $UserData['data']->log_id
                            ),
                            'class' => __CLASS__
                        ));
                    } else {
                        $error_count++;
                    }
                }



                foreach ($parameter['kandungan'] as $KandKey => $KandValue) {
                    $kandungan_worker = self::$query->insert('master_inv_obat_kandungan', array(
                        'uid_obat' => $uid,
                        'kandungan' => $KandValue['kandungan'],
                        'keterangan' => $KandValue['keterangan'],
                        'created_at' => parent::format_date(),
                        'updated_at' => parent::format_date()
                    ))
                        ->execute();
                }


                //Paket barang
                foreach ($parameter['paket'] as $paketKey => $paketValue) {
                    //check
                    $checkPaket = self::$query->select('master_inv_paket', array(
                        'id'
                    ))
                        ->where(array(
                            'master_inv_paket.parent_barang' => '= ?',
                            'AND',
                            'master_inv_paket.barang' => '= ?'
                        ), array(
                            $uid,
                            $paketValue['barang']
                        ))
                        ->execute();
                    if(count($checkPaket['response_data']) > 0) {
                        //update
                        $proceedPaket = self::$query->update('master_inv_paket', array(
                            'qty' => $paketValue['qty'],
                            'updated_at' => parent::format_date(),
                            'deleted_at' => NULL
                        ))
                            ->where(array(
                                'master_inv_paket.parent_barang' => '= ?',
                                'AND',
                                'master_inv_paket.barang' => '= ?',
                                'AND',
                                'master_inv_paket.id' => '= ?'
                            ), array(
                                $uid,
                                $paketValue['barang'],
                                $checkPaket['response_data'][0]['id']
                            ))
                            ->execute();
                    } else {
                        //insert
                        $proceedPaket = self::$query->insert('master_inv_paket', array(
                            'barang' => $paketValue['barang'],
                            'parent_barang' => $uid,
                            'qty' => $paketValue['qty'],
                            'created_at' => parent::format_date(),
                            'updated_at' => parent::format_date()
                        ))
                            ->execute();
                    }
                }

            } else {
                $error_count++;
            }
        }
        return $error_count;
    }


    private function edit_item($parameter)
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);
        $error_count = 0;
        $uid = $parameter['uid'];
        $old = self::get_item_detail($uid);
        $worker = self::$query->update('master_inv', array(
            'uid' => $uid,
            'kode_barang' => $parameter['kode'],
            'het' => floatval($parameter['het']),
            'nama' => $parameter['nama'],
            'kategori' => $parameter['kategori'],
            'satuan_terkecil' => $parameter['satuan_terkecil'],
            'keterangan' => $parameter['keterangan'],
            'created_at' => parent::format_date(),
            'updated_at' => parent::format_date()
        ))
            ->where(array(
                'master_inv.deleted_at' => 'IS NULL',
                'AND',
                'master_inv.uid' => '= ?'
            ), array(
                $uid
            ))
            ->execute();
        if ($worker['response_result'] > 0) {
            $log = parent::log(array(
                'type' => 'activity',
                'column' => array(
                    'unique_target',
                    'user_uid',
                    'table_name',
                    'action',
                    'old_value',
                    'new_value',
                    'logged_at',
                    'status',
                    'login_id'
                ),
                'value' => array(
                    $uid,
                    $UserData['data']->uid,
                    'master_inv',
                    'U',
                    json_encode($old),
                    json_encode($parameter),
                    parent::format_date(),
                    'N',
                    $UserData['data']->log_id
                ),
                'class' => __CLASS__
            ));

            //Image Upload
            $data = $parameter['image'];
            list($type, $data) = explode(';', $data);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data);
            if (!file_exists('../images/produk')) {
                mkdir('../images/produk');
            }

            file_put_contents('../images/produk/' . $uid . '.png', $data);

            //Kategori Obat
            $oldKategoriObat = self::$query->select('master_inv_obat_kategori_item', array(
                'id',
                'kategori'
            ))
                ->where(array(
                    'master_inv_obat_kategori_item.obat' => '= ?'
                ), array(
                    $uid
                ))
                ->execute();

            //Delete unused kategori
            foreach ($oldKategoriObat['response_data'] as $key => $value) {
                if (!in_array($value['kategori'], $parameter['listKategoriObat'])) {
                    $deleteKategoriObat = self::$query->update('master_inv_obat_kategori_item', array(
                        'deleted_at' => parent::format_date()
                    ))
                        ->where(array(
                            'master_inv_obat_kategori_item.obat' => '= ?',
                            'AND',
                            'master_inv_obat_kategori_item.kategori' => '= ?'
                        ), array(
                            $uid,
                            $value['kategori']
                        ))
                        ->execute();

                    if ($deleteKategoriObat['response_result'] > 0) {
                        //
                    } else {
                        $error_count++;
                    }
                }
            }


            foreach ($parameter['listKategoriObat'] as $key => $value) {
                //Check existing
                $checkKategoriObat = self::$query->select('master_inv_obat_kategori_item', array(
                    'id'
                ))
                    ->where(array(
                        'obat' => '= ?',
                        'AND',
                        'kategori' => '= ?'
                    ), array(
                        $uid,
                        $value
                    ))
                    ->execute();
                if (count($checkKategoriObat['response_data']) > 0) {
                    $kategoriObat = self::$query->update('master_inv_obat_kategori_item', array(
                        'deleted_at' => NULL,
                        'updated_at' => parent::format_date()
                    ))
                        ->where(array(
                            'master_inv_obat_kategori_item.id' => '= ?'
                        ), array(
                            $checkKategoriObat['response_data'][0]['id']
                        ))
                        ->execute();
                    if ($kategoriObat['response_result'] > 0) {
                        $log = parent::log(array(
                            'type' => 'activity',
                            'column' => array(
                                'unique_target',
                                'user_uid',
                                'table_name',
                                'action',
                                'new_value',
                                'logged_at',
                                'status',
                                'login_id'
                            ),
                            'value' => array(
                                $parameter['uid'],
                                $UserData['data']->uid,
                                'master_inv_obat_kategori_item',
                                'U',
                                'activated',
                                parent::format_date(),
                                'N',
                                $UserData['data']->log_id
                            ),
                            'class' => __CLASS__
                        ));
                    } else {
                        $error_count++;
                    }
                } else {
                    $kategoriObat = self::$query->insert('master_inv_obat_kategori_item', array(
                        'obat' => $uid,
                        'kategori' => $value,
                        'created_at' => parent::format_date(),
                        'updated_at' => parent::format_date()
                    ))
                        ->execute();
                    if ($kategoriObat['response_result'] > 0) {
                        $log = parent::log(array(
                            'type' => 'activity',
                            'column' => array(
                                'unique_target',
                                'user_uid',
                                'table_name',
                                'action',
                                'new_value',
                                'logged_at',
                                'status',
                                'login_id'
                            ),
                            'value' => array(
                                $parameter['uid'],
                                $UserData['data']->uid,
                                'master_inv_obat_kategori_item',
                                'I',
                                json_encode($parameter['listKategoriObat']),
                                parent::format_date(),
                                'N',
                                $UserData['data']->log_id
                            ),
                            'class' => __CLASS__
                        ));
                    } else {
                        $error_count++;
                    }
                }
            }


            //Satuan Konversi
            $resetSatuan = self::$query->update('master_inv_satuan_konversi', array(
                'deleted_at' => parent::format_date()
            ))
                ->where(array(
                    'master_inv_satuan_konversi.barang' => '= ?'
                ), array(
                    $uid
                ))
                ->execute();
            $requestSatuanIDs = array();
            $oldSatuanMeta = array();
            $oldSatuanKonversi = self::$query->select('master_inv_satuan_konversi', array(
                'id',
                'barang',
                'dari_satuan',
                'ke_satuan'
            ))
                ->where(array(
                    'master_inv_satuan_konversi.barang' => '= ?'
                ), array(
                    $uid
                ))
                ->execute();

            foreach ($oldSatuanKonversi['response_data'] as $key => $value) {
                if (!in_array($value['id'], $requestSatuanIDs)) {
                    array_push($requestSatuanIDs, $value['id']);
                    array_push($oldSatuanMeta, $value);
                }
            }

            foreach ($parameter['satuanKonversi'] as $key => $value) {
                if (isset($requestSatuanIDs[$key])) {
                    $updateKonversi = self::$query->update('master_inv_satuan_konversi', array(
                        'barang' => $uid,
                        'dari_satuan' => $value['dari'],
                        'rasio' => $value['rasio'],
                        'ke_satuan' => $value['ke'],
                        'updated_at' => parent::format_date(),
                        'deleted_at' => NULL
                    ))
                        ->where(array(
                            'master_inv_satuan_konversi.id' => '= ?'
                        ), array(
                            $requestSatuanIDs[$key]
                        ))
                        ->execute();
                    if ($updateKonversi['response_result'] > 0) {
                        $log = parent::log(array(
                            'type' => 'activity',
                            'column' => array(
                                'unique_target',
                                'user_uid',
                                'table_name',
                                'action',
                                'old_value',
                                'new_value',
                                'logged_at',
                                'status',
                                'login_id'
                            ),
                            'value' => array(
                                $parameter['uid'],
                                $UserData['data']->uid,
                                'master_inv_satuan_konversi',
                                'U',
                                json_encode($oldSatuanMeta[$key]),
                                json_encode($parameter['satuanKonversi']),
                                parent::format_date(),
                                'N',
                                $UserData['data']->log_id
                            ),
                            'class' => __CLASS__
                        ));
                    } else {
                        $error_count++;
                    }
                } else {
                    $newKonversi = self::$query->insert('master_inv_satuan_konversi', array(
                        'barang' => $uid,
                        'dari_satuan' => $value['dari'],
                        'rasio' => $value['rasio'],
                        'ke_satuan' => $value['ke'],
                        'created_at' => parent::format_date(),
                        'updated_at' => parent::format_date()
                    ))
                        ->execute();
                    if ($newKonversi['response_result'] > 0) {
                        $log = parent::log(array(
                            'type' => 'activity',
                            'column' => array(
                                'unique_target',
                                'user_uid',
                                'table_name',
                                'action',
                                'new_value',
                                'logged_at',
                                'status',
                                'login_id'
                            ),
                            'value' => array(
                                $parameter['uid'],
                                $UserData['data']->uid,
                                'master_inv_satuan_konversi',
                                'I',
                                json_encode($parameter['satuanKonversi']),
                                parent::format_date(),
                                'N',
                                $UserData['data']->log_id
                            ),
                            'class' => __CLASS__
                        ));
                    } else {
                        $error_count++;
                    }
                }
            }


            //Penjamin
            $resetPenjamin = self::$query->update('master_inv_harga', array(
                'deleted_at' => parent::format_date()
            ))
                ->where(array(
                    'master_inv_harga.barang' => '= ?'
                ), array(
                    $uid
                ))
                ->execute();

            $requestPenjaminIDs = array();
            $oldPenjaminMeta = array();
            $oldPenjaminKonversi = self::$query->select('master_inv_harga', array(
                'id',
                'barang',
                'penjamin'
            ))
                ->where(array(
                    'master_inv_harga.barang' => '= ?'
                ), array(
                    $uid
                ))
                ->execute();

            foreach ($oldPenjaminKonversi['response_data'] as $key => $value) {
                if (!in_array($value['id'], $requestPenjaminIDs)) {
                    array_push($requestPenjaminIDs, $value['id']);
                    array_push($oldPenjaminMeta, $value);
                }
            }

            foreach ($parameter['penjaminList'] as $key => $value) {
                if (isset($requestPenjaminIDs[$key])) {
                    $updatePenjamin = self::$query->update('master_inv_harga', array(
                        'penjamin' => $value['penjamin'],
                        'profit' => $value['marginValue'],
                        'profit_type' => $value['marginType'],
                        'updated_at' => parent::format_date(),
                        'deleted_at' => NULL
                    ))
                        ->where(array(
                            'master_inv_harga.id' => '= ?'
                        ), array(
                            $requestPenjaminIDs[$key]
                        ))
                        ->execute();
                    if ($updatePenjamin['response_result'] > 0) {
                        $log = parent::log(array(
                            'type' => 'activity',
                            'column' => array(
                                'unique_target',
                                'user_uid',
                                'table_name',
                                'action',
                                'old_value',
                                'new_value',
                                'logged_at',
                                'status',
                                'login_id'
                            ),
                            'value' => array(
                                $parameter['uid'],
                                $UserData['data']->uid,
                                'master_inv_harga',
                                'U',
                                json_encode($oldPenjaminMeta[$key]),
                                json_encode($parameter['penjaminList']),
                                parent::format_date(),
                                'N',
                                $UserData['data']->log_id
                            ),
                            'class' => __CLASS__
                        ));
                    } else {
                        $error_count++;
                    }
                } else {
                    $newPenjamin = self::$query->insert('master_inv_harga', array(
                        'barang' => $uid,
                        'penjamin' => $value['penjamin'],
                        'profit' => $value['marginValue'],
                        'profit_type' => $value['marginType'],
                        'created_at' => parent::format_date(),
                        'updated_at' => parent::format_date()
                    ))
                        ->execute();
                    if ($newPenjamin['response_result'] > 0) {
                        $log = parent::log(array(
                            'type' => 'activity',
                            'column' => array(
                                'unique_target',
                                'user_uid',
                                'table_name',
                                'action',
                                'new_value',
                                'logged_at',
                                'status',
                                'login_id'
                            ),
                            'value' => array(
                                $parameter['uid'],
                                $UserData['data']->uid,
                                'master_inv_harga',
                                'I',
                                json_encode($parameter['penjaminList']),
                                parent::format_date(),
                                'N',
                                $UserData['data']->log_id
                            ),
                            'class' => __CLASS__
                        ));
                    } else {
                        $error_count++;
                    }
                }
            }

            //Gudang Rak
            $resetGudangRak = self::$query->update('master_inv_gudang_rak', array(
                'deleted_at' => parent::format_date()
            ))
                ->where(array(
                    'master_inv_gudang_rak.barang' => '= ?'
                ), array(
                    $uid
                ))
                ->execute();

            $requestGudangRakIDs = array();
            $oldGudangRakMeta = array();
            $oldGudangRak = self::$query->select('master_inv_gudang_rak', array(
                'id',
                'barang',
                'gudang',
                'rak'
            ))
                ->where(array(
                    'master_inv_gudang_rak.barang' => '= ?'
                ), array(
                    $uid
                ))
                ->execute();

            foreach ($oldGudangRak['response_data'] as $key => $value) {
                if (!in_array($value['id'], $requestGudangRakIDs)) {
                    array_push($requestGudangRakIDs, $value['id']);
                    array_push($oldGudangRakMeta, $value);
                }
            }

            foreach ($parameter['gudangMeta'] as $key => $value) {
                if (isset($requestGudangRakIDs[$key])) {
                    $updateGudangRak = self::$query->update('master_inv_gudang_rak', array(
                        'barang' => $uid,
                        'gudang' => $value['gudang'],
                        'rak' => $value['lokasi'],
                        'updated_at' => parent::format_date(),
                        'deleted_at' => NULL
                    ))
                        ->where(array(
                            'master_inv_gudang_rak.id' => '= ?'
                        ), array(
                            $requestGudangRakIDs[$key]
                        ))
                        ->execute();
                    if ($updateGudangRak['response_result'] > 0) {
                        $log = parent::log(array(
                            'type' => 'activity',
                            'column' => array(
                                'unique_target',
                                'user_uid',
                                'table_name',
                                'action',
                                'old_value',
                                'new_value',
                                'logged_at',
                                'status',
                                'login_id'
                            ),
                            'value' => array(
                                $parameter['uid'],
                                $UserData['data']->uid,
                                'master_inv_gudang_rak',
                                'U',
                                json_encode($oldGudangRakMeta[$key]),
                                json_encode($parameter['gudangMeta']),
                                parent::format_date(),
                                'N',
                                $UserData['data']->log_id
                            ),
                            'class' => __CLASS__
                        ));
                    } else {
                        $error_count++;
                    }
                } else {
                    $newGudangRak = self::$query->insert('master_inv_gudang_rak', array(
                        'barang' => $uid,
                        'gudang' => $value['gudang'],
                        'rak' => $value['lokasi'],
                        'created_at' => parent::format_date(),
                        'updated_at' => parent::format_date()
                    ))
                        ->execute();
                    if ($newGudangRak['response_result'] > 0) {
                        $log = parent::log(array(
                            'type' => 'activity',
                            'column' => array(
                                'unique_target',
                                'user_uid',
                                'table_name',
                                'action',
                                'new_value',
                                'logged_at',
                                'status',
                                'login_id'
                            ),
                            'value' => array(
                                $parameter['uid'],
                                $UserData['data']->uid,
                                'master_inv_gudang_rak',
                                'I',
                                json_encode($parameter['gudangMeta']),
                                parent::format_date(),
                                'N',
                                $UserData['data']->log_id
                            ),
                            'class' => __CLASS__
                        ));
                    } else {
                        $error_count++;
                    }
                }
            }

            //Monitoring
            $resetMonitoring = self::$query->update('master_inv_monitoring', array(
                'deleted_at' => parent::format_date()
            ))
                ->where(array(
                    'master_inv_monitoring.barang' => '= ?'
                ), array(
                    $uid
                ))
                ->execute();

            $requestMonitoringIDs = array();
            $oldMonitoringMeta = array();
            $oldMonitoring = self::$query->select('master_inv_monitoring', array(
                'id',
                'barang',
                'gudang',
                'min',
                'max'
            ))
                ->where(array(
                    'master_inv_monitoring.barang' => '= ?'
                ), array(
                    $uid
                ))
                ->execute();

            foreach ($oldMonitoring['response_data'] as $key => $value) {
                if (!in_array($value['id'], $requestMonitoringIDs)) {
                    array_push($requestMonitoringIDs, $value['id']);
                    array_push($oldMonitoringMeta, $value);
                }
            }

            foreach ($parameter['monitoring'] as $key => $value) {
                if (isset($requestMonitoringIDs[$key])) {
                    $updateMonitoring = self::$query->update('master_inv_monitoring', array(
                        'barang' => $uid,
                        'gudang' => $value['gudang'],
                        'min' => $value['min'],
                        'max' => $value['max'],
                        'updated_at' => parent::format_date(),
                        'deleted_at' => NULL
                    ))
                        ->where(array(
                            'master_inv_monitoring.id' => '= ?'
                        ), array(
                            $requestMonitoringIDs[$key]
                        ))
                        ->execute();
                    if ($updateMonitoring['response_result'] > 0) {
                        $log = parent::log(array(
                            'type' => 'activity',
                            'column' => array(
                                'unique_target',
                                'user_uid',
                                'table_name',
                                'action',
                                'old_value',
                                'new_value',
                                'logged_at',
                                'status',
                                'login_id'
                            ),
                            'value' => array(
                                $parameter['uid'],
                                $UserData['data']->uid,
                                'master_inv_monitoring',
                                'U',
                                json_encode($oldMonitoringMeta[$key]),
                                json_encode($parameter['monitoring']),
                                parent::format_date(),
                                'N',
                                $UserData['data']->log_id
                            ),
                            'class' => __CLASS__
                        ));
                    } else {
                        $error_count++;
                    }
                } else {
                    $newMonitoring = self::$query->insert('master_inv_monitoring', array(
                        'barang' => $uid,
                        'gudang' => $value['gudang'],
                        'min' => $value['min'],
                        'max' => $value['max'],
                        'created_at' => parent::format_date(),
                        'updated_at' => parent::format_date()
                    ))
                        ->execute();
                    if ($newMonitoring['response_result'] > 0) {
                        $log = parent::log(array(
                            'type' => 'activity',
                            'column' => array(
                                'unique_target',
                                'user_uid',
                                'table_name',
                                'action',
                                'new_value',
                                'logged_at',
                                'status',
                                'login_id'
                            ),
                            'value' => array(
                                $parameter['uid'],
                                $UserData['data']->uid,
                                'master_inv_monitoring',
                                'I',
                                json_encode($parameter['monitoring']),
                                parent::format_date(),
                                'N',
                                $UserData['data']->log_id
                            ),
                            'class' => __CLASS__
                        ));
                    } else {
                        $error_count++;
                    }
                }
            }



            //Kandungan

            //hard_delete
            $old_kandungan = self::$query->hard_delete('master_inv_obat_kandungan')
                ->where(array(
                    'master_inv_obat_kandungan.uid_obat' => '= ?'
                ), array(
                    $uid
                ))
                ->execute();

            foreach ($parameter['kandungan'] as $KandKey => $KandValue) {
                $kandungan_worker = self::$query->insert('master_inv_obat_kandungan', array(
                    'uid_obat' => $uid,
                    'kandungan' => $KandValue['kandungan'],
                    'keterangan' => $KandValue['keterangan'],
                    'created_at' => parent::format_date(),
                    'updated_at' => parent::format_date()
                ))
                    ->execute();
            }
            $paketStatus = array();

            $resetPaket = self::$query->update('master_inv_paket', array(
                'deleted_at' => parent::format_date()
            ))
                ->where(array(
                    'master_inv_paket.parent_barang' => '= ?'
                ), array(
                    $uid
                ))
                ->execute();

            //Paket barang
            foreach ($parameter['paket'] as $paketKey => $paketValue) {
                //check
                $checkPaket = self::$query->select('master_inv_paket', array(
                    'id'
                ))
                    ->where(array(
                        'master_inv_paket.parent_barang' => '= ?',
                        'AND',
                        'master_inv_paket.barang' => '= ?'
                    ), array(
                        $uid,
                        $paketValue['barang']
                    ))
                    ->execute();
                if(count($checkPaket['response_data']) > 0) {
                    //update
                    $proceedPaket = self::$query->update('master_inv_paket', array(
                        'qty' => $paketValue['qty'],
                        'updated_at' => parent::format_date(),
                        'deleted_at' => NULL
                    ))
                        ->where(array(
                            'master_inv_paket.parent_barang' => '= ?',
                            'AND',
                            'master_inv_paket.barang' => '= ?',
                            'AND',
                            'master_inv_paket.id' => '= ?'
                        ), array(
                            $uid,
                            $paketValue['barang'],
                            $checkPaket['response_data'][0]['id']
                        ))
                        ->execute();
                } else {
                    //insert
                    $proceedPaket = self::$query->insert('master_inv_paket', array(
                        'barang' => $paketValue['barang'],
                        'parent_barang' => $uid,
                        'qty' => floatval($paketValue['qty']),
                        'created_at' => parent::format_date(),
                        'updated_at' => parent::format_date()
                    ))
                        ->execute();
                }
                array_push($paketStatus, $proceedPaket);
            }
        } else {
            $error_count++;
        }
        return $error_count;
    }

    private function tambah_amprah($parameter)
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);

        //Get Last AI tahun ini
        $lastID = self::$query->select('inventori_amprah', array(
            'uid'
        ))
            ->where(array(
                'EXTRACT(year FROM inventori_amprah.created_at)' => '= ?'
            ), array(
                date('Y')
            ))
            ->execute();

        /*$Unit = new Unit(self::$pdo);
		$UnitInfo = $Unit::get_unit_detail();*/

        $uid = parent::gen_uuid();
        $worker = self::$query->insert('inventori_amprah', array(
            'uid' => $uid,
            'kode_amprah' => str_pad(count($lastID['response_data']) + 1, 5, '0', STR_PAD_LEFT) . '/' . $UserData['data']->unit_kode . '/AMP/' . date('m') . '/' . date('Y'),
            'unit' => $UserData['data']->unit,
            'pegawai' => $UserData['data']->uid,
            'tanggal' => $parameter['tanggal'],
            'status' => 'N',
            'created_at' => parent::format_date(),
            'updated_at' => parent::format_date(),
            'keterangan' => $parameter['keterangan']
        ))
            ->execute();
        if ($worker['response_result'] > 0) {
            $worker['amprah_detail'] = array();
            $log = parent::log(array(
                'type' => 'activity',
                'column' => array(
                    'unique_target',
                    'user_uid',
                    'table_name',
                    'action',
                    'new_value',
                    'logged_at',
                    'status',
                    'login_id'
                ),
                'value' => array(
                    $uid,
                    $UserData['data']->uid,
                    'inventori_amprah',
                    'I',
                    json_encode($parameter),
                    parent::format_date(),
                    'N',
                    $UserData['data']->log_id
                ),
                'class' => __CLASS__
            ));

            foreach ($parameter['item'] as $key => $value) {
                //Amprah Detail
                $worker_detail = self::$query->insert('inventori_amprah_detail', array(
                    'amprah' => $uid,
                    'item' => $key,
                    'jumlah' => floatval($value['qty']),
                    'created_at' => parent::format_date(),
                    'updated_at' => parent::format_date()
                ))
                    ->returning('id')
                    ->execute();

                if ($worker_detail['response_result'] > 0) {
                    $log = parent::log(array(
                        'type' => 'activity',
                        'column' => array(
                            'unique_target',
                            'user_uid',
                            'table_name',
                            'action',
                            'new_value',
                            'logged_at',
                            'status',
                            'login_id'
                        ),
                        'value' => array(
                            $worker_detail['response_unique'],
                            $UserData['data']->uid,
                            'inventori_amprah_detail',
                            'I',
                            json_encode($parameter['item']),
                            parent::format_date(),
                            'N',
                            $UserData['data']->log_id
                        ),
                        'class' => __CLASS__
                    ));
                }
                array_push($worker['amprah_detail'], $worker_detail);
            }
        }
        return $worker;
    }

    private function get_amprah_request($parameter, $status = 'P')
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);

        if (isset($parameter['search']['value']) && !empty($parameter['search']['value'])) {
            if ($UserData['data']->unit == __UNIT_GUDANG__) {
                if ($status == 'S') {
                    $paramData = array(
                        'inventori_amprah.deleted_at' => 'IS NULL',
                        'AND',
                        'inventori_amprah.tanggal' => 'BETWEEN ? AND ?',
                        'AND',
                        'inventori_amprah.status' => '= ?',
                        'AND',
                        'inventori_amprah.kode_amprah' => 'ILIKE ' . '\'%' . $parameter['search']['value'] . '%\''
                    );

                    $paramValue = array(
                        $parameter['from'], $parameter['to'], 'S'
                    );
                } else {
                    $paramData = array(
                        'inventori_amprah.deleted_at' => 'IS NULL',
                        'AND',
                        'inventori_amprah.tanggal' => 'BETWEEN ? AND ?',
                        'AND',
                        'NOT inventori_amprah.status' => '= ?',
                        'AND',
                        'inventori_amprah.kode_amprah' => 'ILIKE ' . '\'%' . $parameter['search']['value'] . '%\''
                    );

                    $paramValue = array(
                        $parameter['from'], $parameter['to'], 'S'
                    );
                }
            } else {
                if ($status == 'S') {
                    $paramData = array(
                        'inventori_amprah.deleted_at' => 'IS NULL',
                        'AND',
                        'inventori_amprah.tanggal' => 'BETWEEN ? AND ?',
                        'AND',
                        'inventori_amprah.unit' => '= ?',
                        'AND',
                        'inventori_amprah.status' => '= ?',
                        'AND',
                        'inventori_amprah.kode_amprah' => 'ILIKE ' . '\'%' . $parameter['search']['value'] . '%\''
                    );

                    $paramValue = array(
                        $parameter['from'], $parameter['to'], $UserData['data']->unit, 'S'
                    );
                } else {
                    $paramData = array(
                        'inventori_amprah.deleted_at' => 'IS NULL',
                        'AND',
                        'inventori_amprah.tanggal' => 'BETWEEN ? AND ?',
                        'AND',
                        'inventori_amprah.unit' => '= ?',
                        'AND',
                        'NOT inventori_amprah.status' => '= ?',
                        'AND',
                        'inventori_amprah.kode_amprah' => 'ILIKE ' . '\'%' . $parameter['search']['value'] . '%\''
                    );

                    $paramValue = array(
                        $parameter['from'], $parameter['to'], $UserData['data']->unit, 'S'
                    );
                }
            }
        } else {
            if ($UserData['data']->unit == __UNIT_GUDANG__) {
                if ($status == 'S') {
                    $paramData = array(
                        'inventori_amprah.deleted_at' => 'IS NULL',
                        'AND',
                        'inventori_amprah.status' => '= ?',
                        'AND',
                        'inventori_amprah.tanggal' => 'BETWEEN ? AND ?'
                    );

                    $paramValue = array(
                        'S', $parameter['from'], $parameter['to']
                    );
                } else {
                    $paramData = array(
                        'inventori_amprah.deleted_at' => 'IS NULL',
                        'AND',
                        'NOT inventori_amprah.status' => '= ?',
                        'AND',
                        'inventori_amprah.tanggal' => 'BETWEEN ? AND ?'
                    );

                    $paramValue = array(
                        'S', $parameter['from'], $parameter['to']
                    );
                }
            } else {
                if ($status == 'S') {
                    $paramData = array(
                        'inventori_amprah.deleted_at' => 'IS NULL',
                        'AND',
                        'inventori_amprah.unit' => '= ?',
                        'AND',
                        'inventori_amprah.status' => '= ?',
                        'AND',
                        'inventori_amprah.tanggal' => 'BETWEEN ? AND ?'
                    );

                    $paramValue = array(
                        $UserData['data']->unit, 'S', $parameter['from'], $parameter['to']
                    );
                } else {
                    $paramData = array(
                        'inventori_amprah.deleted_at' => 'IS NULL',
                        'AND',
                        'inventori_amprah.unit' => '= ?',
                        'AND',
                        'NOT inventori_amprah.status' => '= ?',
                        'AND',
                        'inventori_amprah.tanggal' => 'BETWEEN ? AND ?'
                    );

                    $paramValue = array(
                        $UserData['data']->unit, 'S', $parameter['from'], $parameter['to']
                    );
                }
            }
        }


        if ($parameter['length'] < 0) {
            $data = self::$query->select('inventori_amprah', array(
                'uid',
                'unit',
                'kode_amprah',
                'pegawai',
                'diproses',
                'tanggal',
                'status',
                'created_at',
                'updated_at'
            ))
                ->where($paramData, $paramValue)
                ->execute();
        } else {

            $data = self::$query->select('inventori_amprah', array(
                'uid',
                'unit',
                'kode_amprah',
                'pegawai',
                'diproses',
                'tanggal',
                'status',
                'created_at',
                'updated_at'
            ))
                ->where($paramData, $paramValue)
                ->offset(intval($parameter['start']))
                ->limit(intval($parameter['length']))
                ->execute();
        }

        $data['response_draw'] = $parameter['draw'];

        $autonum = 1;
        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['autonum'] = $autonum;
            $data['response_data'][$key]['tanggal'] = date('d F Y', strtotime($value['tanggal']));

            $Pegawai = new Pegawai(self::$pdo);
            $data['response_data'][$key]['pegawai'] = $Pegawai->get_detail($value['pegawai'])['response_data'][0];
            $data['response_data'][$key]['diproses'] = ((empty($value['diproses'])) ? "-" : $Pegawai::get_detail($value['pegawai'])['response_data'][0]);


            if ($value['status'] == 'N') {
                $statusParse = 'Baru';
            } else if ($value['status'] == 'S') {
                $statusParse = 'Selesai';
            } else {
                $statusParse = 'Ditolak';
            }
            $data['response_data'][$key]['status_caption'] = $statusParse;
            $autonum++;
        }

        $dataTotal = self::$query->select('inventori_amprah', array(
            'uid',
            'unit',
            'kode_amprah',
            'pegawai',
            'diproses',
            'tanggal',
            'status',
            'created_at',
            'updated_at'
        ))
            ->where($paramData, $paramValue)
            ->execute();

        $data['recordsTotal'] = count($dataTotal['response_data']);
        $data['recordsFiltered'] = count($dataTotal['response_data']);
        $data['length'] = intval($parameter['length']);
        $data['start'] = intval($parameter['start']);

        return $data;
    }

    public function get_amprah_detail($parameter)
    {
        $data = self::$query->select('inventori_amprah', array(
            'uid',
            'unit',
            'pegawai',
            'kode_amprah',
            'diproses',
            'keterangan',
            'tanggal',
            'created_at',
            'updated_at'
        ))
            ->where(array(
                'inventori_amprah.uid' => '= ?',
                'AND',
                'inventori_amprah.deleted_at' => 'IS NULL'
            ), array(
                $parameter
            ))
            ->execute();
        if (count($data['response_data']) > 0) {
            $data['response_data'][0]['tanggal'] = date('d F Y  [H:i]', strtotime($data['response_data'][0]['created_at']));
            $Pegawai = new Pegawai(self::$pdo);
            $data['response_data'][0]['pegawai_detail'] = $Pegawai::get_detail($data['response_data'][0]['pegawai'])['response_data'][0];
            $Unit = new Unit(self::$pdo);
            $data['response_data'][0]['pegawai_detail']['unit_detail'] = $Unit::get_unit_detail($data['response_data'][0]['pegawai_detail']['unit'])['response_data'][0];
            $data['response_data'][0]['diproses_detail'] = $Pegawai::get_detail($data['response_data'][0]['diproses'])['response_data'][0];

            $data_detail = self::$query->select('inventori_amprah_detail', array(
                'id',
                'amprah',
                'item',
                'jumlah',
                'created_at',
                'updated_at'
            ))
                ->where(array(
                    'inventori_amprah_detail.deleted_at' => 'IS NULL',
                    'AND',
                    'inventori_amprah_detail.amprah' => '= ?'
                ), array(
                    $parameter
                ))
                ->execute();
            if (count($data_detail['response_data']) > 0) {
                $detail_amprah = array();
                foreach ($data_detail['response_data'] as $key => $value) {
                    //Batch Gudang
                    $value['batch'] = self::get_item_batch($value['item']);

                    $ItemDetail = self::get_item_detail($value['item'])['response_data'][0];
                    $ItemDetail['satuan_terkecil'] = self::get_satuan_detail($ItemDetail['satuan_terkecil'])['response_data'][0];
                    $value['item'] = $ItemDetail;

                    array_push($detail_amprah, $value);
                }
                $data['response_data'][0]['amprah_detail'] = $detail_amprah;
            }
        }
        return $data;
    }

    private function proses_amprah($parameter)
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);
        $uid = parent::gen_uuid();

        //Get Last AI tahun ini
        $lastID = self::$query->select('inventori_amprah_proses', array(
            'uid'
        ))
            ->where(array(
                'EXTRACT(year FROM inventori_amprah_proses.created_at)' => '= ?'
            ), array(
                date('Y')
            ))
            ->execute();

        $worker = self::$query->insert('inventori_amprah_proses', array(
            'uid' => $uid,
            'kode' => str_pad(count($lastID['response_data']) + 1, 5, '0', STR_PAD_LEFT) . '/' . $UserData['data']->unit_kode . '/AMP-OUT/' . date('m') . '/' . date('Y'),
            'amprah' => $parameter['amprah'],
            'pegawai' => $UserData['data']->uid,
            'tanggal' => parent::format_date(),
            'created_at' => parent::format_date(),
            'updated_at' => parent::format_date()
        ))
            ->execute();
        if ($worker['response_result'] > 0) {
            $log = parent::log(array(
                'type' => 'activity',
                'column' => array(
                    'unique_target',
                    'user_uid',
                    'table_name',
                    'action',
                    'new_value',
                    'logged_at',
                    'status',
                    'login_id'
                ),
                'value' => array(
                    $uid,
                    $UserData['data']->uid,
                    'inventori_amprah_proses',
                    'I',
                    json_encode($parameter),
                    parent::format_date(),
                    'N',
                    $UserData['data']->log_id
                ),
                'class' => __CLASS__
            ));

            //Save Detail
            foreach ($parameter['data'] as $key => $value) {
                foreach ($value['batch'] as $BKey => $BValue) {
                    if(floatval($BValue['disetujui']) > 0) { //Yg 0 ngapain catat bambang
                        $amprah_proses_detail = self::$query->insert('inventori_amprah_proses_detail', array(
                            'amprah_proses' => $uid,
                            'item' => $key,
                            'batch' => $BValue['batch'],
                            'qty' => $BValue['disetujui'],
                            'keterangan' => $parameter['data'][$key]['keterangan'],
                            'created_at' => parent::format_date(),
                            'updated_at' => parent::format_date()
                        ))
                            ->returning('id')
                            ->execute();

                        if ($amprah_proses_detail['response_result'] > 0) {
                            $log = parent::log(array(
                                'type' => 'activity',
                                'column' => array(
                                    'unique_target',
                                    'user_uid',
                                    'table_name',
                                    'action',
                                    'new_value',
                                    'logged_at',
                                    'status',
                                    'login_id'
                                ),
                                'value' => array(
                                    $amprah_proses_detail['response_unique'],
                                    $UserData['data']->uid,
                                    'inventori_amprah_proses_detail',
                                    'I',
                                    json_encode($parameter['data']['batch']),
                                    parent::format_date(),
                                    'N',
                                    $UserData['data']->log_id
                                ),
                                'class' => __CLASS__
                            ));


                            //Proses Kurang Stok
                            $lastStockMinus = self::$query->select('inventori_stok', array(
                                'stok_terkini'
                            ))
                                ->where(array(
                                    'inventori_stok.barang' => '= ?',
                                    'AND',
                                    'inventori_stok.batch' => '= ?',
                                    'AND',
                                    'inventori_stok.gudang' => '= ?'
                                ), array(
                                    $key, $BValue['batch'], __GUDANG_UTAMA__
                                ))
                                ->execute();
                            $terkiniMinus = $lastStockMinus['response_data'][0]['stok_terkini'] - $BValue['disetujui'];
                            $minus_stock = self::$query->update('inventori_stok', array(
                                'stok_terkini' => $terkiniMinus
                            ))
                                ->where(array(
                                    'inventori_stok.barang' => '= ?',
                                    'AND',
                                    'inventori_stok.batch' => '= ?',
                                    'AND',
                                    'inventori_stok.gudang' => '= ?'
                                ), array(
                                    $key, $BValue['batch'], __GUDANG_UTAMA__
                                ))
                                ->execute();

                            if ($minus_stock['response_result'] > 0) {
                                $stok_log = self::$query->insert('inventori_stok_log', array(
                                    'barang' => $key,
                                    'batch' => $BValue['batch'],
                                    'gudang' => __GUDANG_UTAMA__,
                                    'masuk' => 0,
                                    'keluar' => $BValue['disetujui'],
                                    'saldo' => $terkiniMinus,
                                    'type' => __STATUS_AMPRAH__,
                                    'jenis_transaksi' => 'inventori_amprah_proses',
                                    'uid_foreign' => $uid,
                                    'keterangan' => 'Proses amprah'
                                ))
                                    ->execute();
                            }

                            //Proses Tambah Stok
                            //Dapatkan stok point
                            $Unit = new Unit(self::$pdo);
                            $UnitDetail = $Unit::get_unit_detail($parameter['dari_unit']);

                            $lastStockPlus = self::$query->select('inventori_stok', array(
                                'stok_terkini'
                            ))
                                ->where(array(
                                    'inventori_stok.barang' => '= ?',
                                    'AND',
                                    'inventori_stok.batch' => '= ?',
                                    'AND',
                                    'inventori_stok.gudang' => '= ?'
                                ), array(
                                    $key, $BValue['batch'], $UnitDetail['response_data'][0]['gudang']
                                ))
                                ->execute();
                            $terkiniPlus = $lastStockPlus['response_data'][0]['stok_terkini'] + $BValue['disetujui'];

                            //Check Apakah stock point ada ?
                            $check_stok_point = self::$query->select('inventori_stok', array(
                                'id'
                            ))
                                ->where(array(
                                    'inventori_stok.barang' => '= ?',
                                    'AND',
                                    'inventori_stok.batch' => '= ?',
                                    'AND',
                                    'inventori_stok.gudang' => '= ?'
                                ), array(
                                    $key, $BValue['batch'], $UnitDetail['response_data'][0]['gudang']
                                ))
                                ->execute();
                            if (count($check_stok_point['response_data']) > 0) {
                                $plus_stock = self::$query->update('inventori_stok', array(
                                    'stok_terkini' => $terkiniPlus
                                ))
                                    ->where(array(
                                        'inventori_stok.barang' => '= ?',
                                        'AND',
                                        'inventori_stok.batch' => '= ?',
                                        'AND',
                                        'inventori_stok.gudang' => '= ?'
                                    ), array(
                                        $key, $BValue['batch'], $UnitDetail['response_data'][0]['gudang']
                                    ))
                                    ->execute();
                            } else {
                                $plus_stock = self::$query->insert('inventori_stok', array(
                                    'barang' => $key,
                                    'batch' => $BValue['batch'],
                                    'gudang' => $UnitDetail['response_data'][0]['gudang'],
                                    'stok_terkini' => $terkiniPlus
                                ))
                                    ->execute();
                            }

                            if ($plus_stock['response_result'] > 0) {
                                $stok_log = self::$query->insert('inventori_stok_log', array(
                                    'barang' => $key,
                                    'batch' => $BValue['batch'],
                                    'gudang' => $UnitDetail['response_data'][0]['gudang'],
                                    'masuk' => $BValue['disetujui'],
                                    'keluar' => 0,
                                    'saldo' => $terkiniPlus,
                                    'type' => __STATUS_AMPRAH__,
                                    'jenis_transaksi' => 'inventori_amprah_proses',
                                    'uid_foreign' => $uid,
                                    'keterangan' => 'Proses amprah'
                                ))
                                    ->execute();
                            }


                            if ($minus_stock['response_result'] > 0 && $plus_stock['response_result'] > 0) {
                                //Update Status amprah menjadi selesai
                                $update_amprah = self::$query->update('inventori_amprah', array(
                                    'status' => 'S',
                                    'updated_at' => parent::format_date()
                                ))
                                    ->where(array(
                                        'inventori_amprah.deleted_at' => 'IS NULL',
                                        'AND',
                                        'inventori_amprah.uid' => '= ?'
                                    ), array(
                                        $parameter['amprah']
                                    ))
                                    ->execute();
                            }
                        }
                    }
                }
            }
        }

        return $worker;
    }

    private function get_amprah_proses_detail($parameter)
    {
        $data = self::$query->select('inventori_amprah_proses', array(
            'uid',
            'kode',
            'amprah',
            'pegawai',
            'tanggal',
            'created_at'
        ))
            ->where(array(
                'inventori_amprah_proses.deleted_at' => 'IS NULL',
                'AND',
                'inventori_amprah_proses.amprah' => '= ?'
            ), array(
                $parameter
            ))
            ->execute();

        $Inventori = new Inventori(self::$pdo);
        $Pegawai = new Pegawai(self::$pdo);

        foreach ($data['response_data'] as $key => $value) {
            $amprah_detail = self::get_amprah_detail($value['amprah']);
            $data['response_data'][$key]['amprah'] = $amprah_detail['response_data'][0];
            $data['response_data'][$key]['tanggal'] = date('d F Y [H:i]', strtotime($value['created_at']));
            $PegawaiDetail = $Pegawai::get_detail($value['pegawai']);
            $data['response_data'][$key]['pegawai'] = $PegawaiDetail['response_data'][0];
            $detail_proses = self::$query->select('inventori_amprah_proses_detail', array(
                'id',
                'item',
                'batch',
                'keterangan',
                'qty'
            ))
                ->where(array(
                    'inventori_amprah_proses_detail.deleted_at' => 'IS NULL',
                    'AND',
                    'inventori_amprah_proses_detail.amprah_proses' => '= ?'
                ), array(
                    $value['uid']
                ))
                ->execute();
            $autonum = 1;
            foreach ($detail_proses['response_data'] as $DKey => $DValue) {
                $detail_proses['response_data'][$DKey]['item'] = $Inventori->get_item_detail($DValue['item'])['response_data'][0];
                $detail_proses['response_data'][$DKey]['batch'] = $Inventori->get_batch_detail($DValue['batch'])['response_data'][0];
                $detail_proses['response_data'][$DKey]['autonum'] = $autonum;
                $autonum++;
            }
            $data['response_data'][$key]['detail'] = $detail_proses['response_data'];
        }

        return $data;
    }

    public function get_stok()
    {
        $data = self::$query->select('inventori_stok', array(
            'id',
            'barang',
            'batch',
            'gudang',
            'stok_terkini'
        ))
            ->execute();
        $autonum = 1;
        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['autonum'] = $autonum;
            $data['response_data'][$key]['barang'] = self::get_item_detail($value['barang'])['response_data'][0];
            $data['response_data'][$key]['batch'] = self::get_batch_detail($value['batch'])['response_data'][0];
            $data['response_data'][$key]['batch']['expired_date'] = date('d F Y', strtotime($data['response_data'][$key]['batch']['expired_date']));

            $autonum++;
        }
        return $data;
    }

    private function get_stok_batch_unit($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);

        if (isset($parameter['search']['value']) && !empty($parameter['search']['value'])) {
            $paramData = array(
                'inventori_stok.barang' => '= ?',
                'AND',
                'inventori_stok.gudang' => '= ?',
                'AND',
                'inventori_batch.batch' => 'ILIKE ' . '\'%' . $parameter['search']['value'] . '%\''
            );

            $paramValue = array($parameter['barang'], $parameter['gudang']);
        } else {
            $paramData = array(
                'inventori_stok.barang' => '= ?',
                'AND',
                'inventori_stok.gudang' => '= ?'
            );

            $paramValue = array($parameter['barang'], $parameter['gudang']);
        }


        if ($parameter['length'] < 0) {
            $data = self::$query->select('inventori_stok', array(
                'id',
                'barang',
                'batch',
                'gudang',
                'stok_terkini'
            ))
                ->join('inventori_batch', array(
                    'batch',
                    'expired_date'
                ))
                ->on(array(
                    array(
                        'inventori_stok.batch', '=', 'inventori_batch.uid'
                    )
                ))
                ->order(array(
                    'inventori_batch.expired_date' => 'ASC'
                ))
                ->where($paramData, $paramValue)
                ->execute();
        } else {
            $data = self::$query->select('inventori_stok', array(
                'id',
                'barang',
                'batch',
                'gudang',
                'stok_terkini'
            ))
                ->join('inventori_batch', array(
                    'batch',
                    'expired_date'
                ))
                ->on(array(
                    array(
                        'inventori_stok.batch', '=', 'inventori_batch.uid'
                    )
                ))
                ->order(array(
                    'inventori_batch.expired_date' => 'ASC'
                ))
                ->where($paramData, $paramValue)
                ->offset(intval($parameter['start']))
                ->limit(intval($parameter['length']))
                ->execute();
        }

        $data['response_draw'] = $parameter['draw'];
        $autonum = intval($parameter['start']) + 1;
        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['autonum'] = $autonum;
            $data['response_data'][$key]['barang'] = self::get_item_detail($value['barang'])['response_data'][0];
            $data['response_data'][$key]['expired_date'] = date('d F Y', strtotime($value['expired_date']));
            //$data['response_data'][$key]['batch']['expired_date'] = date('d F Y', strtotime($data['response_data'][$key]['batch']['expired_date']));

            $autonum++;
        }

        $itemTotal = self::$query->select('inventori_stok', array(
            'id'
        ))
            ->where($paramData, $paramValue)
            ->execute();

        $data['recordsTotal'] = count($itemTotal['response_data']);
        $data['recordsFiltered'] = count($itemTotal['response_data']);
        $data['length'] = intval($parameter['length']);
        $data['start'] = intval($parameter['start']);

        return $data;


    }

    private function get_stok_log_backend($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);

        if (isset($parameter['search']['value']) && !empty($parameter['search']['value'])) {
            $paramData = array(
                'inventori_stok_log.type' => '= ?',
                'AND',
                'inventori_stok_log.gudang' => '= ?',
                'AND',
                'master_inv.nama' => 'ILIKE ' . '\'%' . $parameter['search']['value'] . '%\''
            );

            $paramValue = array(__STATUS_STOK_AWAL__, $parameter['gudang']);
        } else {
            $paramData = array(
                'inventori_stok_log.type' => '= ?',
                'AND',
                'inventori_stok_log.gudang' => '= ?'
            );

            $paramValue = array(__STATUS_STOK_AWAL__, $parameter['gudang']);
        }


        if ($parameter['length'] < 0) {
            $data = self::$query->select('inventori_stok_log', array(
                'id',
                'barang',
                'batch',
                'gudang',
                'masuk',
                'keluar',
                'saldo'
            ))
                ->join('master_inv', array(
                    'nama'
                ))
                ->on(array(
                    array('inventori_stok_log.barang', '=' , 'master_inv.uid')
                ))
                ->where($paramData, $paramValue)
                ->execute();
        } else {
            $data = self::$query->select('inventori_stok_log', array(
                'id',
                'barang',
                'batch',
                'gudang',
                'masuk',
                'keluar',
                'saldo'
            ))
                ->join('master_inv', array(
                    'nama'
                ))
                ->on(array(
                    array('inventori_stok_log.barang', '=' , 'master_inv.uid')
                ))
                ->where($paramData, $paramValue)
                ->offset(intval($parameter['start']))
                ->limit(intval($parameter['length']))
                ->execute();
        }

        $data['response_draw'] = $parameter['draw'];
        $autonum = intval($parameter['start']) + 1;
        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['autonum'] = $autonum;
            $data['response_data'][$key]['barang'] = self::get_item_detail($value['barang'])['response_data'][0];
            $data['response_data'][$key]['batch'] = self::get_batch_detail($value['batch'])['response_data'][0];
            $data['response_data'][$key]['batch']['expired_date'] = date('d F Y', strtotime($data['response_data'][$key]['batch']['expired_date']));

            $autonum++;
        }

        $itemTotal = self::$query->select('inventori_stok_log', array(
            'id'
        ))
            ->where($paramData, $paramValue)
            ->execute();

        $data['recordsTotal'] = count($itemTotal['response_data']);
        $data['recordsFiltered'] = count($itemTotal['response_data']);
        $data['length'] = intval($parameter['length']);
        $data['start'] = intval($parameter['start']);

        return $data;
    }

    public function get_stok_log()
    {
        $data = self::$query->select('inventori_stok_log', array(
            'id',
            'barang',
            'batch',
            'gudang',
            'masuk',
            'keluar',
            'saldo'
        ))
            ->where(array(
                'inventori_stok_log.type' => '= ?'
            ), array(
                __STATUS_STOK_AWAL__
            ))
            ->execute();

        $autonum = 1;
        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['autonum'] = $autonum;
            $data['response_data'][$key]['barang'] = self::get_item_detail($value['barang'])['response_data'][0];
            $data['response_data'][$key]['batch'] = self::get_batch_detail($value['batch'])['response_data'][0];
            $data['response_data'][$key]['batch']['expired_date'] = date('d F Y', strtotime($data['response_data'][$key]['batch']['expired_date']));

            $autonum++;
        }
        return $data;
    }

    private function tambah_stok_awal($parameter)
    {
        //Check ketersediaan batch
        $batchCheck = self::$query->select('inventori_batch', array(
            'uid'
        ))
            ->where(array(
                'inventori_batch.barang' => '= ?',
                'AND',
                'inventori_batch.batch' => '= ?'
            ), array(
                $parameter['item'],
                $parameter['batch']
            ))
            ->execute();

        if (count($batchCheck['response_data']) > 0) {
            $targetBatch = $batchCheck['response_data'][0]['uid'];
            $batchEnabling = self::$query->update('inventori_batch', array(
                'deleted_at' => NULL
            ))
                ->where(array(
                    'inventori_batch.uid' => '= ?'
                ), array(
                    $targetBatch
                ))
                ->execute();

        } else {
            $targetBatch = parent::gen_uuid();
            $batchNew = self::$query->insert('inventori_batch', array(
                'uid' => $targetBatch,
                'barang' => $parameter['item'],
                'batch' => strtoupper($parameter['batch']),
                'expired_date' => date('Y-m-d', strtotime($parameter['exp'])),
                'created_at' => parent::format_date(),
                'updated_at' => parent::format_date()
            ))
                ->execute();
        }

        $worker = self::$query->select('inventori_stok', array(
            'id',
            'stok_terkini'
        ))
            ->where(array(
                'inventori_stok.barang' => '= ?',
                'AND',
                'inventori_stok.batch' => '= ?',
                'AND',
                'inventori_stok.gudang' => '= ?'
            ), array(
                $parameter['item'],
                $targetBatch,
                $parameter['gudang']
            ))
            ->execute();

        if (count($worker['response_data']) > 0) {
            $endTotal = ($worker['response_data'][0]['stok_terkini'] + $parameter['qty']);
            $proceedStokPoint = self::$query->update('inventori_stok', array(
                'stok_terkini' => $endTotal
            ))
                ->where(array(
                    'inventori_stok.barang' => '= ?',
                    'AND',
                    'inventori_stok.batch' => '= ?',
                    'AND',
                    'inventori_stok.gudang' => '= ?'
                ), array(
                    $parameter['item'],
                    $targetBatch,
                    $parameter['gudang']
                ))
                ->execute();
        } else {
            $endTotal = $parameter['qty'];
            $proceedStokPoint = self::$query->insert('inventori_stok', array(
                'barang' => $parameter['item'],
                'batch' => $targetBatch,
                'gudang' => $parameter['gudang'],
                'stok_terkini' => $parameter['qty']
            ))
                ->execute();
        }

        if ($proceedStokPoint['response_result'] > 0) {
            //Stok Log
            $stok_log_worker = self::$query->insert('inventori_stok_log', array(
                'barang' => $parameter['item'],
                'batch' => $targetBatch,
                'gudang' => $parameter['gudang'],
                'masuk' => $parameter['qty'],
                'keluar' => 0,
                'saldo' => $endTotal,
                'type' => __STATUS_STOK_AWAL__
            ))
                ->execute();
        }

        return $proceedStokPoint;
    }

    private function get_stok_gudang($parameter)
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);

        if (isset($parameter['search']['value']) && !empty($parameter['search']['value'])) {
            $paramData = array(
                'inventori_stok.gudang' => '= ?',
                'AND',
                'master_inv.nama' => 'ILIKE ' . '\'%' . $parameter['search']['value'] . '%\''
            );

            $paramValue = array(
                //$UserData['data']->gudang
                $parameter['gudang']
            );
        } else {
            $paramData = array(
                'inventori_stok.gudang' => '= ?'
            );

            $paramValue = array(
                $parameter['gudang']
                //$UserData['data']->gudang
            );
        }


        if ($parameter['length'] < 0) {
            $data = self::$query->select('inventori_stok', array(
                'id',
                'barang',
                'batch',
                'gudang',
                'stok_terkini'
            ))
                ->join('master_inv', array(
                    'uid as uid_item',
                    'nama',
                    'satuan_terkecil'
                ))
                ->on(array(
                    array('inventori_stok.barang', '=', 'master_inv.uid')
                ))
                ->where($paramData, $paramValue)
                ->execute();
        } else {
            $data = self::$query->select('inventori_stok', array(
                'id',
                'barang',
                'batch',
                'gudang',
                'stok_terkini'
            ))
                ->join('master_inv', array(
                    'uid',
                    'nama',
                    'satuan_terkecil'
                ))
                ->on(array(
                    array('inventori_stok.barang', '=', 'master_inv.uid')
                ))
                ->where($paramData, $paramValue)
                ->offset(intval($parameter['start']))
                ->limit(intval($parameter['length']))
                ->execute();
        }

        $data['response_draw'] = $parameter['draw'];

        $autonum = 1;
        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['autonum'] = $autonum;
            $data['response_data'][$key]['satuan_terkecil'] = self::get_satuan_detail($value['satuan_terkecil'])['response_data'][0];
            $data['response_data'][$key]['batch'] = self::get_batch_detail($value['batch'])['response_data'][0];
            $data['response_data'][$key]['batch']['expired_date'] = date('d F Y', strtotime($data['response_data'][$key]['batch']['expired_date']));
            $autonum++;
        }

        $dataTotal = self::$query->select('inventori_stok', array(
            'id',
            'barang',
            'batch',
            'gudang',
            'stok_terkini'
        ))
            ->join('master_inv', array(
                'uid',
                'nama'
            ))
            ->on(array(
                array('inventori_stok.barang', '=', 'master_inv.uid')
            ))
            ->where($paramData, $paramValue)
            ->execute();

        $data['recordsTotal'] = count($dataTotal['response_data']);
        $data['recordsFiltered'] = count($dataTotal['response_data']);
        $data['length'] = intval($parameter['length']);
        $data['start'] = intval($parameter['start']);

        return $data;
    }

    private function get_opname_history($parameter)
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);

        if (isset($parameter['search']['value']) && !empty($parameter['search']['value'])) {
            $paramData = array(
                'inventori_stok_opname.gudang' => '= ?',
                'AND',
                'inventori_stok_opname.kode' => 'ILIKE ' . '\'%' . $parameter['search']['value'] . '%\''
            );

            $paramValue = array(
                $UserData['data']->gudang
            );
        } else {
            $paramData = array(
                'inventori_stok_opname.gudang' => '= ?',
            );

            $paramValue = array(
                $UserData['data']->gudang
            );
        }


        if ($parameter['length'] < 0) {
            $data = self::$query->select('inventori_stok_opname', array(
                'uid',
                'kode',
                'dari',
                'sampai',
                'pegawai',
                'gudang',
                'keterangan',
                'created_at',
                'updated_at'
            ))
                ->where($paramData, $paramValue)
                ->execute();
        } else {
            $data = self::$query->select('inventori_stok_opname', array(
                'uid',
                'kode',
                'dari',
                'sampai',
                'pegawai',
                'gudang',
                'keterangan',
                'created_at',
                'updated_at'
            ))
                ->where($paramData, $paramValue)
                ->offset(intval($parameter['start']))
                ->limit(intval($parameter['length']))
                ->execute();
        }

        $data['response_draw'] = $parameter['draw'];

        $autonum = 1;
        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['autonum'] = $autonum;
            $Pegawai = new Pegawai(self::$pdo);
            $PegawaiDetail = $Pegawai::get_detail($value['pegawai'])['response_data'][0];
            $data['response_data'][$key]['pegawai'] = $PegawaiDetail;

            $data['response_data'][$key]['dari'] = date('d F Y', strtotime($value['dari']));
            $data['response_data'][$key]['sampai'] = date('d F Y', strtotime($value['sampai']));

            $data['response_data'][$key]['created_at'] = date('d F Y / H:i:s', strtotime($value['created_at']));
            $autonum++;
        }

        $dataTotal = self::$query->select('inventori_stok_opname', array(
            'uid',
            'kode',
            'dari',
            'sampai',
            'pegawai',
            'gudang',
            'keterangan',
            'created_at',
            'updated_at'
        ))
            ->where($paramData, $paramValue)
            ->execute();

        $data['recordsTotal'] = count($dataTotal['response_data']);
        $data['recordsFiltered'] = count($dataTotal['response_data']);
        $data['length'] = intval($parameter['length']);
        $data['start'] = intval($parameter['start']);

        return $data;
    }

    private function tambah_opname($parameter)
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);
        //Get Last AI tahun ini
        $lastID = self::$query->select('inventori_stok_opname', array(
            'uid'
        ))
            ->where(array(
                'EXTRACT(year FROM inventori_stok_opname.created_at)' => '= ?'
            ), array(
                date('Y')
            ))
            ->execute();
        $uid = parent::gen_uuid();
        $worker = self::$query->insert('inventori_stok_opname', array(
            'uid' => $uid,
            'kode' => str_pad(count($lastID['response_data']) + 1, 5, '0', STR_PAD_LEFT) . '/' . $UserData['data']->unit_kode . '/OPN/' . date('m') . '/' . date('Y'),
            'dari' => date('Y-m-d', strtotime($parameter['dari'])),
            'sampai' => date('Y-m-d', strtotime($parameter['sampai'])),
            'pegawai' => $UserData['data']->uid,
            'gudang' => $UserData['data']->gudang,
            'keterangan' => $parameter['keterangan'],
            'created_at' => parent::format_date(),
            'updated_at' => parent::format_date()
        ))
            ->execute();

        if ($worker['response_result'] > 0) {
            foreach ($parameter['item'] as $key => $value) {
                $opname_detail = self::$query->insert('inventori_stok_opname_detail', array(
                    'opname' => $uid,
                    'item' => $key,
                    'batch' => $value['batch'],
                    'qty_awal' => $value['qty_awal'],
                    'qty_akhir' => $value['nilai'],
                    'keterangan' => $value['keterangan'],
                    'created_at' => parent::format_date(),
                    'updated_at' => parent::format_date()
                ))
                    ->execute();

                if ($opname_detail['response_result'] >= 0) {
                    //Update Stok
                    $updateStok = self::$query->update('inventori_stok', array(
                        'stok_terkini' => floatval($value['nilai'])
                    ))
                        ->where(array(
                            'inventori_stok.barang' => '= ?',
                            'AND',
                            'inventori_stok.batch' => '= ?',
                            'AND',
                            'inventori_stok.gudang' => '= ?'
                        ), array(
                            $key, $value['batch'], $UserData['data']->gudang
                        ))
                        ->execute();
                    if ($updateStok['response_result'] > 0) {
                        if (floatval($value['nilai']) > floatval($value['qty_awal'])) {
                            $stok_log = self::$query->insert('inventori_stok_log', array(
                                'barang' => $key,
                                'batch' => $value['batch'],
                                'gudang' => $UserData['data']->gudang,
                                'masuk' => floatval($value['qty_awal']) - floatval($value['nilai']),
                                'keluar' => 0,
                                'saldo' => floatval($value['nilai']),
                                'type' => __STATUS_OPNAME__
                            ))
                                ->execute();
                        } else {
                            $stok_log = self::$query->insert('inventori_stok_log', array(
                                'barang' => $key,
                                'batch' => $value['batch'],
                                'gudang' => $UserData['data']->gudang,
                                'masuk' => 0,
                                'keluar' => floatval($value['nilai']) - floatval($value['qty_awal']),
                                'saldo' => floatval($value['nilai']),
                                'type' => __STATUS_OPNAME__
                            ))
                                ->execute();
                        }
                    }
                }
            }

            $log = parent::log(array(
                'type' => 'activity',
                'column' => array(
                    'unique_target',
                    'user_uid',
                    'table_name',
                    'action',
                    'logged_at',
                    'status',
                    'login_id'
                ),
                'value' => array(
                    $uid,
                    $UserData['data']->uid,
                    'inventori_stok_opname',
                    'I',
                    parent::format_date(),
                    'N',
                    $UserData['data']->log_id
                ),
                'class' => __CLASS__
            ));
        }

        return $worker;
    }

    private function get_opname_detail($parameter)
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);

        $data = self::$query->select('inventori_stok_opname', array(
            'uid',
            'kode',
            'dari',
            'sampai',
            'pegawai',
            'created_at',
            'updated_at',
            'gudang',
            'keterangan'
        ))
            ->where(array(
                'inventori_stok_opname.deleted_at' => 'IS NULL',
                'AND',
                'inventori_stok_opname.uid' => '= ?',
                'AND',
                'inventori_stok_opname.gudang' => '= ?'
            ), array(
                $parameter,
                $UserData['data']->gudang
            ))
            ->execute();

        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['dari'] = date('d F Y', strtotime($value['dari']));
            $data['response_data'][$key]['sampai'] = date('d F Y', strtotime($value['sampai']));
            $Pegawai = new Pegawai(self::$pdo);
            $PegawaiDetail = $Pegawai::get_detail($value['pegawai'])['response_data'][0];
            $data['response_data'][$key]['pegawai'] = $PegawaiDetail;

            $OpnameDetail = self::$query->select('inventori_stok_opname_detail', array(
                'id',
                'item',
                'batch',
                'qty_awal',
                'qty_akhir',
                'keterangan'
            ))
                ->where(array(
                    'inventori_stok_opname_detail.opname' => '= ?',
                    'AND',
                    'inventori_stok_opname_detail.deleted_at' => 'IS NULL'
                ), array(
                    $parameter
                ))
                ->execute();
            $autonum = 1;
            foreach ($OpnameDetail['response_data'] as $OKey => $OValue) {
                $OpnameDetail['response_data'][$OKey]['autonum'] = $autonum;
                $OpnameDetail['response_data'][$OKey]['item'] = self::get_item_detail($OValue['item'])['response_data'][0];
                $OpnameDetail['response_data'][$OKey]['batch'] = self::get_batch_detail($OValue['batch'])['response_data'][0];
                $autonum++;
            }
            $data['response_data'][$key]['detail'] = $OpnameDetail['response_data'];
        }

        return $data;
    }

    private function get_opname_detail_item($parameter)
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);

        if (isset($parameter['search']['value']) && !empty($parameter['search']['value'])) {
            $paramData = array(
                'inventori_stok_opname_detail.opname' => '= ?',
                'AND',
                'master_inv.nama' => 'ILIKE ' . '\'%' . $parameter['search']['value'] . '%\'',
                'AND',
                'inventori_stok_opname_detail.deleted_at' => 'IS NULL'
            );

            $paramValue = array(
                $parameter['uid']
            );
        } else {
            $paramData = array(
                'inventori_stok_opname_detail.opname' => '= ?',
                'AND',
                'inventori_stok_opname_detail.deleted_at' => 'IS NULL'
            );

            $paramValue = array(
                $parameter['uid']
            );
        }


        if ($parameter['length'] < 0) {
            $data = self::$query->select('inventori_stok_opname_detail', array(
                'id',
                'item',
                'batch',
                'qty_awal',
                'qty_akhir',
                'keterangan'
            ))
                ->join('master_inv', array(
                    'uid',
                    'nama'
                ))
                ->on(array(
                    array('inventori_stok_opname_detail.item', '=', 'master_inv.uid')
                ))
                ->where($paramData, $paramValue)
                ->execute();
        } else {
            $data = self::$query->select('inventori_stok_opname_detail', array(
                'id',
                'item',
                'batch',
                'qty_awal',
                'qty_akhir',
                'keterangan'
            ))
                ->join('master_inv', array(
                    'uid as uid_barang',
                    'nama as nama_barang'
                ))
                ->on(array(
                    array('inventori_stok_opname_detail.item', '=', 'master_inv.uid')
                ))
                ->where($paramData, $paramValue)
                ->offset(intval($parameter['start']))
                ->limit(intval($parameter['length']))
                ->execute();
        }

        $data['response_draw'] = $parameter['draw'];

        $autonum = 1;
        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['autonum'] = $autonum;
            $data['response_data'][$key]['batch'] = self::get_batch_detail($value['batch'])['response_data'][0];
            $autonum++;
        }

        $dataTotal = self::$query->select('inventori_stok_opname_detail', array(
            'id',
            'item',
            'batch',
            'qty_awal',
            'qty_akhir',
            'keterangan'
        ))
            ->join('master_inv', array(
                'uid',
                'nama'
            ))
            ->on(array(
                array('inventori_stok_opname_detail.item', '=', 'master_inv.uid')
            ))
            ->where($paramData, $paramValue)
            ->execute();

        $data['recordsTotal'] = count($dataTotal['response_data']);
        $data['recordsFiltered'] = count($dataTotal['response_data']);
        $data['length'] = intval($parameter['length']);
        $data['start'] = intval($parameter['start']);

        return $data;
    }

    private function tambah_mutasi($parameter)
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);

        //Get Last AI tahun ini
        $lastID = self::$query->select('inventori_mutasi', array(
            'uid'
        ))
            ->where(array(
                'EXTRACT(year FROM inventori_mutasi.created_at)' => '= ?'
            ), array(
                date('Y')
            ))
            ->execute();
        $uid = parent::gen_uuid();
        $worker = self::$query->insert('inventori_mutasi', array(
            'uid' => $uid,
            'kode' => str_pad(count($lastID['response_data']) + 1, 5, '0', STR_PAD_LEFT) . '/' . $UserData['data']->unit_kode . '/MUT/' . date('m') . '/' . date('Y'),
            'tanggal' => parent::format_date(),
            'dari' => $parameter['dari'],
            'ke' => $parameter['ke'],
            'keterangan' => $parameter['keterangan'],
            'pegawai' => $UserData['data']->uid,
            'created_at' => parent::format_date(),
            'updated_at' => parent::format_date()
        ))
            ->execute();

        if ($worker['response_result'] > 0) {
            $log = parent::log(array(
                'type' => 'activity',
                'column' => array(
                    'unique_target',
                    'user_uid',
                    'table_name',
                    'action',
                    'logged_at',
                    'status',
                    'login_id'
                ),
                'value' => array(
                    $uid,
                    $UserData['data']->uid,
                    'inventori_mutasi',
                    'I',
                    parent::format_date(),
                    'N',
                    $UserData['data']->log_id
                ),
                'class' => __CLASS__
            ));

            foreach ($parameter['item'] as $key => $value) {
                if (floatval($value['mutasi']) > 0) {
                    $ItemUIDBatch = explode('|', $key);

                    $mutasi_detail = self::$query->insert('inventori_mutasi_detail', array(
                        'mutasi' => $uid,
                        'item' => $ItemUIDBatch[0],
                        'batch' => $ItemUIDBatch[1],
                        'qty' => $value['mutasi'],
                        'keterangan' => $value['keterangan'],
                        'created_at' => parent::format_date(),
                        'updated_at' => parent::format_date()
                    ))
                        ->returning('id')
                        ->execute();


                    if ($mutasi_detail['response_result'] > 0) {
                        $log = parent::log(array(
                            'type' => 'activity',
                            'column' => array(
                                'unique_target',
                                'user_uid',
                                'table_name',
                                'action',
                                'logged_at',
                                'status',
                                'login_id'
                            ),
                            'value' => array(
                                $mutasi_detail['response_unique'],
                                $UserData['data']->uid,
                                'inventori_mutasi_detail',
                                'I',
                                parent::format_date(),
                                'N',
                                $UserData['data']->log_id
                            ),
                            'class' => __CLASS__
                        ));

                        //Update Stok dan Stok Log
                        //Recent Stok
                        //Update Stok Asal
                        $stok_dari_old = self::$query->select('inventori_stok', array(
                            'stok_terkini'
                        ))
                            ->where(array(
                                'inventori_stok.barang' => '= ?',
                                'AND',
                                'inventori_stok.batch' => '= ?',
                                'AND',
                                'inventori_stok.gudang' => '= ?'
                            ), array(
                                $ItemUIDBatch[0],
                                $ItemUIDBatch[1],
                                $parameter['dari']
                            ))
                            ->execute();

                        $update_stok_old_dari = self::$query->update('inventori_stok', array(
                            'stok_terkini' => floatval($stok_dari_old['response_data'][0]['stok_terkini']) - floatval($value['mutasi'])
                        ))
                            ->where(array(
                                'inventori_stok.barang' => '= ?',
                                'AND',
                                'inventori_stok.batch' => '= ?',
                                'AND',
                                'inventori_stok.gudang' => '= ?'
                            ), array(
                                $ItemUIDBatch[0],
                                $ItemUIDBatch[1],
                                $parameter['dari']
                            ))
                            ->execute();

                        if ($update_stok_old_dari['response_result'] > 0) {
                            //Update Stok Log Dari
                            $update_dari_log = self::$query->insert('inventori_stok_log', array(
                                'barang' => $ItemUIDBatch[0],
                                'batch' => $ItemUIDBatch[1],
                                'gudang' => $parameter['dari'],
                                'masuk' => 0,
                                'keluar' => floatval($value['mutasi']),
                                'saldo' => floatval($stok_dari_old['response_data'][0]['stok_terkini']) - floatval($value['mutasi']),
                                'type' => __STATUS_MUTASI_STOK__
                            ))
                                ->execute();
                        }


                        //Update Stok Tujuan
                        $stok_ke_old = self::$query->select('inventori_stok', array(
                            'stok_terkini'
                        ))
                            ->where(array(
                                'inventori_stok.barang' => '= ?',
                                'AND',
                                'inventori_stok.batch' => '= ?',
                                'AND',
                                'inventori_stok.gudang' => '= ?'
                            ), array(
                                $ItemUIDBatch[0],
                                $ItemUIDBatch[1],
                                $parameter['ke']
                            ))
                            ->execute();

                        if (count($stok_ke_old['response_data']) > 0) {
                            $update_stok_old_ke = self::$query->update('inventori_stok', array(
                                'stok_terkini' => floatval($stok_ke_old['response_data'][0]['stok_terkini']) + floatval($value['mutasi'])
                            ))
                                ->where(array(
                                    'inventori_stok.barang' => '= ?',
                                    'AND',
                                    'inventori_stok.batch' => '= ?',
                                    'AND',
                                    'inventori_stok.gudang' => '= ?'
                                ), array(
                                    $ItemUIDBatch[0],
                                    $ItemUIDBatch[1],
                                    $parameter['ke']
                                ))
                                ->execute();
                        } else {
                            $update_stok_old_ke = self::$query->insert('inventori_stok', array(
                                'stok_terkini' => floatval($value['mutasi']),
                                'barang' => $ItemUIDBatch[0],
                                'batch' => $ItemUIDBatch[1],
                                'gudang' => $parameter['ke']
                            ))
                                ->execute();
                        }

                        if ($update_stok_old_ke['response_result'] > 0) {
                            //Update Stok Log Ke
                            $update_dari_log = self::$query->insert('inventori_stok_log', array(
                                'barang' => $ItemUIDBatch[0],
                                'batch' => $ItemUIDBatch[1],
                                'gudang' => $parameter['ke'],
                                'masuk' => floatval($value['mutasi']),
                                'keluar' => 0,
                                'saldo' => floatval($stok_dari_old['response_data'][0]['stok_terkini']) + floatval($value['mutasi']),
                                'type' => __STATUS_MUTASI_STOK__
                            ))
                                ->execute();
                        }
                    }
                }
            }
        }

        return $worker;
    }

    private function master_inv_import_fetch($parameter)
    {
        if (!empty($_FILES['csv_file']['name'])) {
            $unique_name = array();

            $file_data = fopen($_FILES['csv_file']['tmp_name'], 'r');
            $column = fgetcsv($file_data); //array_head
            $row_data = array();
            while ($row = fgetcsv($file_data)) {
                if (!in_array($row[0], $unique_name)) {
                    array_push($unique_name, $row[0]);
                    $column_builder = array();
                    foreach ($column as $key => $value) {
                        $column_builder[$value] = $row[$key];
                    }
                    array_push($row_data, $column_builder);
                }
            }

            $build_col = array();
            foreach ($column as $key => $value) {
                array_push($build_col, array("data" => $value));
            }

            $output = array(
                'column' => $column,
                'row_data' => $row_data,
                'column_builder' => $build_col
            );
            return $output;
        }
    }

    private function proceed_import_master_inv($parameter)
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);

        $duplicate_row = array();
        $non_active = array();
        $success_proceed = 0;
        $proceed_data = array();

        foreach ($parameter['data_import'] as $key => $value) {
            //Check duplicate
            $check = self::$query->select('master_inv', array(
                'uid',
                'nama',
                'deleted_at'
            ))
                ->where(array(
                    'master_inv.nama' => '= ?'
                ), array(
                    $value['nama']
                ))
                ->execute();
            if (count($check['response_data']) > 0) {
                foreach ($check['response_data'] as $CheckKey => $CheckValue) {
                    if($CheckValue['deleted_at'] !== '') {
                        array_push($non_active, $CheckValue);
                    } else {
                        array_push($duplicate_row, $CheckValue);
                    }
                }
            } else { //Item Unik
                //Check Ketersediaan Satuan
                $satuan_check = self::$query->select('master_inv_satuan', array(
                    'uid',
                    'deleted_at'
                ))
                    ->where(array(
                        'master_inv_satuan.nama' => '= ?'
                    ), array(
                        strtoupper($value['satuan'])
                    ))
                    ->limit(1)
                    ->execute();
                if(count($satuan_check['response_data']) > 0) {
                    $targetSatuan = $satuan_check['response_data'][0]['uid'];
                    foreach($satuan_check['response_data'] as $SatuanKey => $SatuanValue) {
                        if($SatuanValue['deleted_at'] != '') {
                            //Aktifkan kembali satuan
                            $activateSatuan = self::$query->update('master_inv_satuan', array(
                                'deleted_at' => NULL
                            ))
                                ->where(array(
                                    'master_inv_satuan.uid' => '= ?'
                                ), array(
                                    $SatuanValue['uid']
                                ))
                                ->execute();
                            if($activateSatuan['response_result'] > 0) {
                                $log = parent::log(array(
                                    'type' => 'activity',
                                    'column' => array(
                                        'unique_target',
                                        'user_uid',
                                        'table_name',
                                        'action',
                                        'new_value',
                                        'logged_at',
                                        'status',
                                        'login_id'
                                    ),
                                    'value' => array(
                                        $SatuanValue['uid'],
                                        $UserData['data']->uid,
                                        'master_inv_satuan',
                                        'U',
                                        'aktifkan kembali',
                                        parent::format_date(),
                                        'N',
                                        $UserData['data']->log_id
                                    ),
                                    'class' => __CLASS__
                                ));
                            }
                        }
                    }

                } else {
                    $targetSatuan = parent::gen_uuid();
                    // Satuan Baru
                    $new_satuan = self::$query->insert('master_inv_satuan', array(
                        'uid' => $targetSatuan,
                        'nama' => strtoupper($value['satuan']),
                        'created_at' => parent::format_date(),
                        'updated_at' => parent::format_date()
                    ))
                        ->execute();
                    if($new_satuan['response_result'] > 0) {
                        $log = parent::log(array(
                            'type' => 'activity',
                            'column' => array(
                                'unique_target',
                                'user_uid',
                                'table_name',
                                'action',
                                'logged_at',
                                'status',
                                'login_id'
                            ),
                            'value' => array(
                                $targetSatuan,
                                $UserData['data']->uid,
                                'master_inv_satuan',
                                'I',
                                parent::format_date(),
                                'N',
                                $UserData['data']->log_id
                            ),
                            'class' => __CLASS__
                        ));
                    }
                }

                //Check Ketersediaan Kategori
                $kategori_check = self::$query->select('master_inv_kategori', array(
                    'uid',
                    'deleted_at'
                ))
                    ->where(array(
                        'master_inv_kategori.nama' => '= ?'
                    ), array(
                        strtoupper($value['kategori'])
                    ))
                    ->limit(1)
                    ->execute();
                if(count($kategori_check['response_data']) > 0) {
                    $targetKategori = $kategori_check['response_data'][0]['uid'];
                    foreach($kategori_check['response_data'] as $KategoriKey => $KategoriValue) {
                        if($KategoriValue['deleted_at'] != '') {
                            //Aktifkan kembali kategori
                            $activateKategori = self::$query->update('master_inv_kategori', array(
                                'deleted_at' => NULL
                            ))
                                ->where(array(
                                    'master_inv_kategori.uid' => '= ?'
                                ), array(
                                    $KategoriValue['uid']
                                ))
                                ->execute();
                            if($activateKategori['response_result'] > 0) {
                                $log = parent::log(array(
                                    'type' => 'activity',
                                    'column' => array(
                                        'unique_target',
                                        'user_uid',
                                        'table_name',
                                        'action',
                                        'new_value',
                                        'logged_at',
                                        'status',
                                        'login_id'
                                    ),
                                    'value' => array(
                                        $KategoriValue['uid'],
                                        $UserData['data']->uid,
                                        'master_inv_kategori',
                                        'U',
                                        'aktifkan kembali',
                                        parent::format_date(),
                                        'N',
                                        $UserData['data']->log_id
                                    ),
                                    'class' => __CLASS__
                                ));
                            }
                        }
                    }
                } else {
                    $targetKategori = parent::gen_uuid();
                    // Kategori Baru
                    $new_kategori = self::$query->insert('master_inv_kategori', array(
                        'uid' => $targetKategori,
                        'nama' => strtoupper($value['kategori']),
                        'created_at' => parent::format_date(),
                        'updated_at' => parent::format_date()
                    ))
                        ->execute();
                    if($new_kategori['response_result'] > 0) {
                        $log = parent::log(array(
                            'type' => 'activity',
                            'column' => array(
                                'unique_target',
                                'user_uid',
                                'table_name',
                                'action',
                                'logged_at',
                                'status',
                                'login_id'
                            ),
                            'value' => array(
                                $targetKategori,
                                $UserData['data']->uid,
                                'master_inv_kategori',
                                'I',
                                parent::format_date(),
                                'N',
                                $UserData['data']->log_id
                            ),
                            'class' => __CLASS__
                        ));
                    }
                }


                $newItemUID = parent::gen_uuid();
                $newItem = self::$query->insert('master_inv', array(
                    'uid' => $newItemUID,
                    'nama' => $value['nama'],
                    'kategori' => $targetKategori,
                    'satuan_terkecil' => $targetSatuan,
                    'created_at' => parent::format_date(),
                    'updated_at' => parent::format_date()
                ))
                    ->execute();

                array_push($proceed_data, $newItem);

                if($newItem['response_result'] > 0) {
                    $log = parent::log(array(
                        'type' => 'activity',
                        'column' => array(
                            'unique_target',
                            'user_uid',
                            'table_name',
                            'action',
                            'logged_at',
                            'status',
                            'login_id'
                        ),
                        'value' => array(
                            $newItemUID,
                            $UserData['data']->uid,
                            'master_inv',
                            'I',
                            parent::format_date(),
                            'N',
                            $UserData['data']->log_id
                        ),
                        'class' => __CLASS__
                    ));

                    $success_proceed += 1;

                    if($parameter['super'] == 'farmasi') {
                        $kategori_obat = array();
                        if (substr($value['antibiotik'], 0, 4) !== 'NON-' || substr($value['antibiotik'], 0, 1) !== 'N') {
                            array_push($kategori_obat, __UID_ANTIBIOTIK__);
                        }

                        if (substr($value['fornas'], 0, 4) !== 'NON-' || substr($value['fornas'], 0, 1) !== 'N') {
                            array_push($kategori_obat, __UID_FORNAS__);
                        }

                        if (substr($value['narkotika'], 0, 4) !== 'NON-' || substr($value['narkotika'], 0, 1) !== 'N') {
                            array_push($kategori_obat, __UID_NARKOTIKA__);
                        }

                        if (substr($value['psikotropika'], 0, 4) !== 'NON-' || substr($value['psikotropika'], 0, 1) !== 'N') {
                            array_push($kategori_obat, __UID_PSIKOTROPIKA__);
                        }

                        if ($value['generik'] !== '') {
                            array_push($kategori_obat, __UID_GENERIK__);
                        }

                        foreach ($kategori_obat as $KatObatKey => $KatObatValue) {
                            $proses_kategori_obat = self::$query->insert('master_inv_obat_kategori_item', array(
                                'obat' => $newItemUID,
                                'kategori' => $KatObatValue,
                                'created_at' => parent::format_date(),
                                'updated_at' => parent::format_date()
                            ))
                                ->execute();
                        }
                    }
                }
            }
        }

        return array(
            'duplicate_row' => $duplicate_row,
            'non_active' => $non_active,
            'success_proceed' => $success_proceed,
            'data' => $parameter['data_import'],
            'proceed' => $proceed_data
        );
    }


    private function get_item_back_end($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);

        if (isset($parameter['search']['value']) && !empty($parameter['search']['value'])) {
            $paramData = array(
                'master_inv.deleted_at' => 'IS NULL',
                'AND',
                'master_inv.nama' => 'ILIKE ' . '\'%' . $parameter['search']['value'] . '%\''
            );

            $paramValue = array();
        } else {
            $paramData = array(
                'master_inv.deleted_at' => 'IS NULL'
            );

            $paramValue = array();
        }


        if ($parameter['length'] < 0) {
            $data = self::$query->select('master_inv', array(
                'uid',
                'kode_barang',
                'nama',
                'kategori',
                'het',
                'satuan_terkecil',
                'created_at',
                'updated_at'
            ))
                ->where($paramData, $paramValue)
                ->execute();
        } else {
            $data = self::$query->select('master_inv', array(
                'uid',
                'kode_barang',
                'nama',
                'kategori',
                'het',
                'satuan_terkecil',
                'created_at',
                'updated_at'
            ))
                ->where($paramData, $paramValue)
                ->offset(intval($parameter['start']))
                ->limit(intval($parameter['length']))
                ->execute();
        }

        $data['response_draw'] = $parameter['draw'];
        $autonum = intval($parameter['start']) + 1;
        foreach ($data['response_data'] as $key => $value) {


            $data['response_data'][$key]['autonum'] = $autonum;
            if(file_exists('../images/produk/' . $value['uid'] . '.png')) {
                $data['response_data'][$key]['image'] = 'images/produk/' . $value['uid'] . '.png';
            } else {
                $data['response_data'][$key]['image'] = 'images/product.png';
            }

            /*$kategori_obat = self::get_kategori_obat_item($value['uid']);
            foreach ($kategori_obat as $KOKey => $KOValue) {
                $kategori_obat[$KOKey]['kategori'] = self::get_kategori_obat_detail($KOValue['kategori'])['response_data'][0]['nama'];
            }*/

            //$data['response_data'][$key]['kategori_obat'] = $kategori_obat;
            $data['response_data'][$key]['satuan_terkecil'] = self::get_satuan_detail($value['satuan_terkecil'])['response_data'][0];
            $data['response_data'][$key]['kategori'] = self::get_kategori_detail($value['kategori'])['response_data'][0];
            //$data['response_data'][$key]['manufacture'] = self::get_manufacture_detail($value['manufacture'])['response_data'][0];

            //Data Penjamin
            /*$PenjaminObat = new Penjamin(self::$pdo);
            $ListPenjaminObat = $PenjaminObat::get_penjamin_obat($value['uid'])['response_data'];
            foreach ($ListPenjaminObat as $PenjaminKey => $PenjaminValue) {
                $ListPenjaminObat[$PenjaminKey]['profit'] = floatval($PenjaminValue['profit']);
            }
            $data['response_data'][$key]['penjamin'] = $ListPenjaminObat;*/

            //Cek Ketersediaan Stok
            /*$TotalStock = 0;
            $InventoriStockPopulator = self::get_item_batch($value['uid']);
            if (count($InventoriStockPopulator['response_data']) > 0) {
                foreach ($InventoriStockPopulator['response_data'] as $TotalKey => $TotalValue) {
                    $TotalStock += floatval($TotalValue['stok_terkini']);
                }
                $data['response_data'][$key]['stok'] = $TotalStock;
                $data['response_data'][$key]['batch'] = $InventoriStockPopulator['response_data'];
            } else {
                $data['response_data'][$key]['stok'] = 0;
            }*/

            $autonum++;
        }

        $itemTotal = self::$query->select('master_inv', array(
            'uid'
        ))
            ->where($paramData, $paramValue)
            ->execute();

        $data['recordsTotal'] = count($itemTotal['response_data']);
        $data['recordsFiltered'] = count($itemTotal['response_data']);
        $data['length'] = intval($parameter['length']);
        $data['start'] = intval($parameter['start']);

        return $data;
    }

    private function get_stok_back_end($parameter)
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);

        if (isset($parameter['search']['value']) && !empty($parameter['search']['value'])) {
            $paramData = array(
                'inventori_stok.gudang' => '= ?',
                'AND',
                'master_inv.nama' => 'ILIKE ' . '\'%' . $parameter['search']['value'] . '%\''
            );

            $paramValue = array($UserData['data']->gudang);
        } else {
            $paramData = array(
                'inventori_stok.gudang' => '= ?'
            );

            $paramValue = array($UserData['data']->gudang);
        }

        if ($parameter['length'] < 0) {
            $data = self::$query->select('inventori_stok', array(
                'id',
                'barang',
                'batch',
                'gudang',
                'stok_terkini'
            ))
                ->join('master_inv', array(
                    'nama'
                ))
                ->on(array(
                    array('inventori_stok.barang', '=', 'master_inv.uid')
                ))
                ->where($paramData, $paramValue)
                ->execute();
        } else {
            $data = self::$query->select('inventori_stok', array(
                'id',
                'barang',
                'batch',
                'gudang',
                'stok_terkini'
            ))
                ->join('master_inv', array(
                    'nama'
                ))
                ->on(array(
                    array('inventori_stok.barang', '=', 'master_inv.uid')
                ))
                ->offset(intval($parameter['start']))
                ->limit(intval($parameter['length']))
                ->where($paramData, $paramValue)
                ->execute();
        }

        $data['response_draw'] = $parameter['draw'];
        $autonum = intval($parameter['start']) + 1;
        foreach ($data['response_data'] as $key => $value) {
            if($value['gudang'] === $UserData['data']->gudang) {
                $data['response_data'][$key]['autonum'] = $autonum;
                $ItemDetail = self::get_item_detail($value['barang'])['response_data'][0];
                $data['response_data'][$key]['detail'] = $ItemDetail;

                if(file_exists('../images/produk/' . $value['barang'] . '.png')) {
                    $data['response_data'][$key]['image'] = 'images/produk/' . $value['barang'] . '.png';
                } else {
                    $data['response_data'][$key]['image'] = 'images/product.png';
                }

                $kategori_obat = self::get_kategori_obat_item($value['barang']);
                foreach ($kategori_obat as $KOKey => $KOValue) {
                    $kategori_obat[$KOKey]['kategori'] = self::get_kategori_obat_detail($KOValue['kategori'])['response_data'][0]['nama'];
                }

                $data['response_data'][$key]['kategori_obat'] = $kategori_obat;
                $data['response_data'][$key]['satuan_terkecil'] = self::get_satuan_detail($value['satuan_terkecil'])['response_data'][0];
                $data['response_data'][$key]['kategori'] = self::get_kategori_detail($value['kategori'])['response_data'][0];
                $data['response_data'][$key]['manufacture'] = self::get_manufacture_detail($value['manufacture'])['response_data'][0];

                //Data Penjamin
                $PenjaminObat = new Penjamin(self::$pdo);
                $ListPenjaminObat = $PenjaminObat::get_penjamin_obat($value['barang'])['response_data'];
                foreach ($ListPenjaminObat as $PenjaminKey => $PenjaminValue) {
                    $ListPenjaminObat[$PenjaminKey]['profit'] = floatval($PenjaminValue['profit']);
                }
                $data['response_data'][$key]['penjamin'] = $ListPenjaminObat;

                //Cek Ketersediaan Stok
                $TotalStock = 0;
                $InventoriStockPopulator = self::get_item_batch($value['barang']);
                if (count($InventoriStockPopulator['response_data']) > 0) {
                    foreach ($InventoriStockPopulator['response_data'] as $TotalKey => $TotalValue) {
                        if($TotalValue['gudang'] === $UserData['data']->gudang) {
                            $TotalStock += floatval($TotalValue['stok_terkini']);
                        }
                    }
                    $data['response_data'][$key]['stok'] = $TotalStock;
                    $data['response_data'][$key]['batch'] = $InventoriStockPopulator['response_data'];
                } else {
                    $data['response_data'][$key]['stok'] = 0;
                }
                $data['response_data'][$key]['batch_info'] = $InventoriStockPopulator;

                $autonum++;
            } else {
                unset($data['response_data'][$key]);
            }
        }

        $itemTotal = self::$query->select('inventori_stok', array(
            'id'
        ))
            ->where($paramData, $paramValue)
            ->execute();

        $data['recordsTotal'] = count($itemTotal['response_data']);
        $data['recordsFiltered'] = count($itemTotal['response_data']);
        $data['length'] = intval($parameter['length']);
        $data['gudang_saya'] = $UserData['data']->gudang;
        $data['start'] = intval($parameter['start']);

        return $data;
    }

    private function satu_harga_profit($parameter)
    {

        $Penjamin = new Penjamin(self::$pdo);
        $PenjaminData = $Penjamin::get_penjamin();
        $proceedData = array();
        $worker = self::$query->select('master_inv', array(
            'uid'
        ))
            ->where(array(
                'master_inv.deleted_at' => 'IS NULL'
            ))
            ->execute();

        foreach ($worker['response_data'] as $key => $value)
        {
            foreach ($PenjaminData['response_data'] as $PKey => $PValue)
            {
                //Check
                $Check = self::$query->select('master_inv_harga', array(
                    'id'
                ))
                    ->where(array(
                        'master_inv_harga.barang' => '= ?',
                        'AND',
                        'master_inv_harga.penjamin' => '= ?',
                        'AND',
                        'master_inv_harga.deleted_at' => 'IS NULL'
                    ), array(
                        $value['uid'],
                        $PValue['uid']
                    ))
                    ->execute();

                if(count($Check['response_data']) > 0)
                {
                    $proceedDataAction = self::$query->update('master_inv_harga', array(
                        'profit' => floatval($parameter['profit']),
                        'profit_type' => $parameter['profit_type']
                    ))
                        ->where(array(
                            'master_inv_harga.barang' => '= ?',
                            'AND',
                            'master_inv_harga.penjamin' => '= ?',
                            'AND',
                            'master_inv_harga.deleted_at' => 'IS NULL'
                        ), array(
                            $value['uid'],
                            $PValue['uid']
                        ))
                        ->execute();
                } else
                {
                    $proceedDataAction = self::$query->insert('master_inv_harga', array(
                        'barang' => $value['uid'],
                        'penjamin' => $PValue['uid'],
                        'profit' => floatval($parameter['profit']),
                        'profit_type' => $parameter['profit_type'],
                        'created_at' => parent::format_date(),
                        'updated_at' => parent::format_date()
                    ))
                        ->execute();
                }

                array_push($proceedData, $proceedDataAction);
            }
        }

        return $proceedData;
    }


    private function get_mutasi_item($parameter) {
        $data = self::$query->select('inventori_mutasi_detail', array(
            'item',
            'batch',
            'qty',
            'keterangan',
            'created_at',
            'updated_at'
        ))
            ->where(array(
                'inventori_mutasi_detail.mutasi' => '= ?',
                'AND',
                'inventori_mutasi_detail.deleted_at' => 'IS NULL'
            ), array(
                $parameter[2]
            ))
            ->execute();
        foreach ($data['response_data'] as $key => $value) {
            //Item Detail
            $Item = self::get_item_detail($value['item']);
            $data['response_data'][$key]['item'] = $Item['response_data'][0];

            //Batch Detail
            $Batch = self::get_batch_detail($value['batch']);
            $data['response_data'][$key]['batch'] = $Batch['response_data'][0];


        }

        return $data;
    }


    private function get_mutasi_request($parameter)
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);

        if (isset($parameter['search']['value']) && !empty($parameter['search']['value'])) {
            $paramData = array(
                'inventori_mutasi.deleted_at' => 'IS NULL',
                'AND',
                'pegawai.nama' => 'ILIKE ' . '\'%' . $parameter['search']['value'] . '%\''
            );

            $paramValue = array();
        } else {
            $paramData = array(
                'inventori_mutasi.deleted_at' => 'IS NULL'
            );

            $paramValue = array();
        }


        if ($parameter['length'] < 0) {
            $data = self::$query->select('inventori_mutasi', array(
                'uid',
                'kode',
                'tanggal',
                'dari',
                'ke',
                'pegawai',
                'keterangan',
                'created_at',
                'updated_at'
            ))
                ->join('pegawai', array(
                    'nama',
                    'unit'
                ))
                ->on(array(
                    array('inventori_mutasi.pegawai', '=', 'pegawai.uid')
                ))
                ->where($paramData, $paramValue)
                ->execute();
        } else {
            $data = self::$query->select('inventori_mutasi', array(
                'uid',
                'kode',
                'tanggal',
                'dari',
                'ke',
                'pegawai',
                'keterangan',
                'created_at',
                'updated_at'
            ))
                ->join('pegawai', array(
                    'nama',
                    'unit'
                ))
                ->on(array(
                    array('inventori_mutasi.pegawai', '=', 'pegawai.uid')
                ))
                ->where($paramData, $paramValue)
                ->offset(intval($parameter['start']))
                ->limit(intval($parameter['length']))
                ->execute();
        }

        $data['response_draw'] = $parameter['draw'];
        $allData = array();
        $autonum = intval($parameter['start']) + 1;
        foreach ($data['response_data'] as $key => $value) {

            //Filter Unit yang sama
            if($value['unit'] == $UserData['data']->unit) {
                $data['response_data'][$key]['autonum'] = $autonum;

                $data['response_data'][$key]['tanggal'] = date('d F Y', strtotime($value['tanggal']));

                //Gudang
                $dari = self::get_gudang_detail($value['dari']);
                $data['response_data'][$key]['dari'] = $dari['response_data'][0];

                $ke = self::get_gudang_detail($value['ke']);
                $data['response_data'][$key]['ke'] = $ke['response_data'][0];

                //Pegawai
                $pegawai = new Pegawai(self::$pdo);
                $data['response_data'][$key]['pegawai'] = $pegawai->get_detail($value['pegawai'])['response_data'][0];

                array_push($allData, $data['response_data'][$key]);
                $autonum++;
            }


        }

        $data['response_data'] = $allData;

        $itemTotal = self::$query->select('inventori_mutasi', array(
            'uid'
        ))
            ->where($paramData, $paramValue)
            ->execute();

        $data['recordsTotal'] = count($itemTotal['response_data']);
        $data['recordsFiltered'] = count($itemTotal['response_data']);
        $data['length'] = intval($parameter['length']);
        $data['start'] = intval($parameter['start']);

        return $data;
    }

//===========================================================================================DELETE
    private function delete($parameter)
    {
        $Authorization = new Authorization();
        $UserData = $Authorization::readBearerToken($parameter['access_token']);

        $worker = self::$query
            ->delete($parameter[6])
            ->where(array(
                $parameter[6] . '.uid' => '= ?'
            ), array(
                $parameter[7]
            ))
            ->execute();
        if ($worker['response_result'] > 0) {
            $log = parent::log(array(
                'type' => 'activity',
                'column' => array(
                    'unique_target',
                    'user_uid',
                    'table_name',
                    'action',
                    'logged_at',
                    'status',
                    'login_id'
                ),
                'value' => array(
                    $parameter[7],
                    $UserData['data']->uid,
                    $parameter[6],
                    'D',
                    parent::format_date(),
                    'N',
                    $UserData['data']->log_id
                ),
                'class' => __CLASS__
            ));
        }
        return $worker;
    }

    private function duplicate_check($parameter)
    {
        return self::$query
            ->select($parameter['table'], array(
                'uid',
                'nama'
            ))
            ->where(array(
                $parameter['table'] . '.deleted_at' => 'IS NULL',
                'AND',
                $parameter['table'] . '.nama' => '= ?'
            ), array(
                $parameter['check']
            ))
            ->execute();
    }

    private function migrate_harga($parameter) {
        $proceed_data = array();
        $dataLama = self::$query->select('strategi_penjualan', array(
            'id', 'produk', 'tanggal',
            'member_cashback', 'member_royalti', 'member_reward', 'member_insentif_personal',
            'stokis_cashback', 'stokis_royalti', 'stokis_reward', 'stokis_insentif_personal',
            'harga_jual_member',
            'discount_type_member',
            'discount_member',
            'harga_akhir_member',

            'harga_jual_stokis',
            'discount_type_stokis',
            'discount_stokis',
            'harga_akhir_stokis'
        ))
            ->where(array(
                'strategi_penjualan.deleted_at' => 'IS NULL',
                'AND',
                'strategi_penjualan.tanggal' => '= ?'
            ), array(
                $parameter['tanggal']
            ))
            ->execute();

        foreach ($dataLama['response_data'] as $key => $value) {
            $Detail = self::get_item_detail($value['produk']);

            //Check Baru
            $check = self::$query->select('master_inv_harga', array(
                'id',
                'produk',
                'member_type',
                'id_table',
                'het',
                'discount_type',
                'discount',
                'jual',
                'created_at',
                'updated_at',
                'cashback',
                'reward',
                'royalti',
                'insentif'
            ))
                ->where(array(
                    'master_inv_harga.produk' => '= ?'
                ), array(
                    $value['produk']
                ))
                ->execute();
            if(count($check['response_data']) > 0) {
                foreach ($check['response_data'] as $newKey => $newValue) {
                    $proceed = self::$query->update('master_inv_harga', array(
                        'het' => $Detail['response_data']['het'],
                        'discount_type' => ($newValue['member_type'] === 'M') ? $value['discount_type_member'] : $value['discount_type_stokis'],
                        'discount' => ($newValue['member_type'] === 'M') ? $value['discount_member'] : $value['discount_stokis'],
                        'jual' => ($newValue['member_type'] === 'M') ? $value['harga_akhir_member'] : $value['harga_akhir_stokis'],
                        'cashback' => ($newValue['member_type'] === 'M') ? $value['member_cashback'] : $value['stokis_cashback'],
                        'reward' => ($newValue['member_type'] === 'M') ? $value['member_reward'] : $value['stokis_reward'],
                        'royalti' => ($newValue['member_type'] === 'M') ? $value['member_royalti'] : $value['stokis_royalti'],
                        'insentif' => ($newValue['member_type'] === 'M') ? $value['member_insentif_personal'] : $value['stokis_insentif_personal'],
                        'updated_at' => parent::format_date(),
                        'deleted_at' => NULL
                    ))
                        ->where(array(
                            'master_inv_harga.produk' => '= ?',
                            'AND',
                            'master_inv_harga.id' => '= ?'
                        ), array(
                            $value['produk'],
                            $newValue['id']
                        ))
                        ->execute();
                }
            } else {
                $proceed_member = self::$query->insert('master_inv_harga', array(
                    'produk' => $value['produk'],
                    'member_type' => 'M',
                    'id_table' => 0,
                    'het' => $Detail['response_data']['het'],
                    'discount_type' => $value['discount_type_member'],
                    'discount' => $value['discount_member'],
                    'jual' => $value['harga_akhir_member'],
                    'cashback' => $value['member_cashback'],
                    'reward' => $value['member_reward'],
                    'royalti' => $value['member_royalti'],
                    'insentif' => $value['member_insentif_personal'],
                    'created_at' => parent::format_date(),
                    'updated_at' => parent::format_date()
                ))
                    ->execute();

                $proceed_stokis = self::$query->insert('master_inv_harga', array(
                    'produk' => $value['produk'],
                    'member_type' => 'S',
                    'id_table' => 0,
                    'het' => $Detail['response_data']['het'],
                    'discount_type' => $value['discount_type_stokis'],
                    'discount' => $value['discount_stokis'],
                    'jual' => $value['harga_akhir_stokis'],
                    'cashback' => $value['stokis_cashback'],
                    'reward' => $value['stokis_reward'],
                    'royalti' => $value['stokis_royalti'],
                    'insentif' => $value['stokis_insentif_personal'],
                    'created_at' => parent::format_date(),
                    'updated_at' => parent::format_date()
                ))
                    ->execute();
            }
        }
        return $proceed_data;
    }
}