<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use DataTables;
use Illuminate\Routing\UrlGenerator;
use Picqer;
use PDF;

class ItemController extends Controller
{
    protected $url;

    public function __construct(UrlGenerator $url)
    {
        $this->url =  $url;   
    }

    public function index(Request $request) {
        $data = array(
            'user' => Session::get('user')
        );

        if($request->ajax()) {
            $model = Barang::query();

            return DataTables::eloquent($model)
            ->addColumn('action', function(Barang $item) {
                return '<a class="btn btn-sm btn-warning" href="'.$this->url->to('/items/edit/'.$item->kd_brg).'"><i class="fas fa-edit"></i> Edit</a> | <a class="btn btn-sm btn-danger" href="'.$this->url->to('/items/delete/'.$item->kd_brg).'" onclick="return confirm(\'Yakin ingin dihapus?\')"><i class="fas fa-trash"></i> Hapus</a>';
            })
            ->toJson();
        }

        return view('pages.items.list', $data);
    }

    public function create() {
        $data = array(
            'user' => Session::get('user')
        );

        return view('pages.items.create', $data);
    }

    public function edit($id) {
        $item = Barang::where('kd_brg', $id)->first();
        $data = array(
            'user' => Session::get('user'),
            'id' => $id,
            'kd_brg' => $item->kd_brg,
            'nm_brg' => $item->nm_brg,
            'hrg_beli' => $item->hrg_beli,
            'hrg_jual' => $item->hrg_jual,
            'jml_brg' => $item->jml_brg,
            'satuan' => $item->satuan
        );

        return view('pages.items.edit
        ', $data);
    }

    public function store(Request $request) {
    
        $request->validate($this->rules(), $this->messages());

        $item = new Barang();
        $item->kd_brg = $request->kd_brg;
        $item->nm_brg = $request->nm_brg;
        $item->hrg_beli = $request->hrg_beli;
        $item->hrg_jual = $request->hrg_jual;
        $item->jml_brg = $request->jml_brg;
        $item->satuan = $request->satuan;

        $success = $item->save();

        if($success) {
            return back()->with('message', 'Data barang berhasil disimpan');
        } else {
            return back()->with('error', 'Data barang gagal disimpan');
        }
    }

    public function update(Request $request) {
        $request->validate($this->rules(), $this->messages());

        $item = Barang::where('kd_brg', $request->id)->first();

        $item->kd_brg = $request->kd_brg;
        $item->nm_brg = $request->nm_brg;
        $item->hrg_beli = $request->hrg_beli;
        $item->hrg_jual = $request->hrg_jual;
        $item->jml_brg = $request->jml_brg;
        $item->satuan = $request->satuan;

        $success = $item->save();

        if($success) {
            return back()->with('message', 'Data barang berhasil diedit');
        } else {
            return back()->with('error', 'Data barang gagal diedit');
        }

    }

    public function delete($id) {
        $item = Barang::where('kd_brg', $id)->first();

        if($item) {
            Barang::destroy($item->kd_brg);

            return back()->with('message', 'Kode barang '.$item->kd_brg.' berhasil dihapus');
        } else {
            return back()->with('error', 'Data barang tidak ditemukan');
        }
    }

    public function rules() {
        $rules = [
            'kd_brg' => ['required','min:4'],
            'nm_brg' => ['required', 'max:100'],
            'hrg_beli' => ['required', 'numeric'],
            'hrg_jual' => ['required', 'numeric'],
            'jml_brg' => ['required', 'numeric'],
            'satuan' => ['required']
        ];

        return $rules;
    }

    public function messages() {
        $messages = [
            'kd_brg.required' => 'Kode barang tidak boleh kosong',
            'kd_brg.min' => 'Kode barang minimal 4 karakter',
            'nm_brg.required' => 'Nama barang tidak boleh kosong',
            'nm_brg.max' => 'Nama barang tidak boleh kosong',
            'hrg_beli.required' => 'Harga beli tidak boleh kosong',
            'hrg_beli.numeric' => 'Harga beli harus berupa angka dan tidak boleh menggunakan huruf', 
            'hrg_jual.required' => 'Harga jual tidak boleh kosong',
            'hrg_jual.numeric' => 'Harga jual harus berupa angka dan tidak boleh menggunakan huruf', 
            'jml_brg.required' => 'Jumlah barang tidak boleh kosong',
            'jml_brg.numeric' => 'Harga jual harus berupa angka dan tidak boleh menggunakan huruf',
            'satuan.required' => 'Satuan barang tidak boleh kosong'
        ];

        return $messages;
    }

    public function generate_barcode($id) {
       
        $data = array(
            'user'=> Session::get('user'),
            'kd_brg' => $id
        );
        return view('pages.items.barcode', $data);
    }

    public function download_barcode($id)
    {
        $data = array(
            'kd_brg' => $id
        );
        $pdf = PDF::loadView('pdf.barcode',$data);


        return $pdf->download('barcode.pdf');
    }

    public function print_barcode($id)
    {
        $data = array(
            'kd_brg' => $id
        );
        $pdf = PDF::loadView('pdf.barcode', $data);

        return $pdf->stream('cetak-barcode.pdf');
    }

    public function send_barcode(Request $request)
    {
        $codes = $request->input('item_codes');
        $data = array(
            'codes' => $codes,
            'success'=> true
        );

        return response()->json($data);
        
    }

    public function preview_barcode($codes)
    {

        $stringArray = $codes;
        $rows = explode(',', $stringArray);
        $result = [];

        if(is_array($rows) || is_object($rows)){
            foreach ($rows as $value) {
                $result[] = $value;
            }

        }
        $data = array(
            'codes' => $result
        );
       
        $pdf = PDF::loadView('pdf.generate_all_barcode', $data)->setOption('A8', 'potrait');

        return $pdf->stream('cetak-barcode.pdf');
        // return view('pdf.generate_all_barcode', $data);
    }
}
