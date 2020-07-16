<?php

namespace App\Http\Controllers\WebBuilder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use yajra\Datatables\Datatables;
use App\Models\PosMenu;
use App\Helpers\ImagesHelper;
use Session;
use Validator;

class MenuController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
      $list_menu = PosMenu::where('menu_place_id', $this->getCurrentPlaceId())->get();
        return view('webbuilder.menus',compact('list_menu'));
    }
    
    public function getMenu(Request $request)
    {
        $menu_item = PosMenu::join('pos_user',function($join){
            $join->on('pos_menu.created_by','=','pos_user.user_id')->on('pos_menu.menu_place_id','=','pos_user.user_place_id');
        })
          ->where('pos_menu.menu_place_id',$this->getCurrentPlaceId())
          ->where('menu_status',1)
          ->select('pos_user.user_nickname','pos_menu.*')
          ->get();

          // echo $menu_item; die();

        return Datatables::of($menu_item)
            ->editColumn('menu_name',function($row){
                return "<a href='".route('menu',$row->menu_id)."'>".$row->menu_name."</a>";
            })
            ->addColumn('parent_name',function($row){
                $parent_item = PosMenu::where('menu_id',$row->menu_parent_id)
                                ->where('menu_place_id',$this->getCurrentPlaceId())
                                ->first();
                if(isset($parent_item->menu_name)){
                    return $parent_item->menu_name ;
                }else { return ""; }
            })
            ->addColumn('menu_type',function($menu_item){
                     $checked = "";
                if ($menu_item->menu_type == 1) {
                    $checked = 'checked';
                }else {
                    // $checked = 'checked';
                }
                    return "<input type='checkbox' value='".$menu_item->menu_id."' id='".$menu_item->menu_id."' class='js-switch show_id' data=".$menu_item->menu_type." ".$checked."/>";
                
                })
            ->editColumn('updated_at',function($row){
                return format_datetime($row->updated_at)." by ".$row->user_nickname;
            })
            ->addColumn('action',function($row){
                return '<a href="'.route('menu',$row->menu_id).'"  class="btn btn-sm btn-secondary" ><i class="fa fa-edit"></i></a>
                        <a href="#" class="delete-menu btn btn-sm btn-secondary" id="'.$row->menu_id.'"><i class="fa fa-trash-o"></i></a>';
            })
            ->rawColumns(['menu_name','menu_type','action'])
            ->make(true);
    }

    
    public function edit(Request $request,$id = 0) {
        $list_menu = PosMenu::where('menu_place_id',$this->getCurrentPlaceId())->where('menu_status',1)->get();
        if($id>0){
            $menu_item = PosMenu::where('menu_place_id',$this->getCurrentPlaceId())
                                ->where('menu_id',$id)
                                ->where('menu_status',1)
                                ->first();
                                // dd($menu_item);
            $menu_date = format_date($menu_item->menu_date);
            return view('webbuilder.menu_edit',compact('list_menu','menu_item','id','menu_date'));
        } else {
            return view('webbuilder.menu_edit',compact('list_menu','id'));
        }    
    }
    
     public function saveMenu(Request $request) {

        $menu_descript = $request->menu_descript == "<p><br></p>" ? "" : $request->menu_descript;
        // dd($request->all());
        $menu_id = $request->menu_id;
        // dd($menu_id);

        $menu_name = $request->menu_name;

        $images="";
        //dd($menu_name);
        if($menu_id >0){ // CHECK EXIST WHEN EDIT
             $check_exist = PosMenu::where('menu_place_id',$this->getCurrentPlaceId())
                                    ->where('menu_id','!=',$menu_id)
                                    ->where('menu_name',$menu_name)
                                    ->where('menu_status',1)
                                    // ->first();
                                    ->count();
                                    // ->count();
        } else {
                $check_exist = PosMenu::where('menu_place_id',$this->getCurrentPlaceId())
                                            ->where('menu_name',$menu_name)
                                            ->where('menu_status',1)
                                            // ->first();   
                                            ->count();
                // if($check_exist>0)
                // {
                //     $request->session()->flash('error','This title already exists.Please enter another title');
                //     return redirect()->back();
                // }
            }
            $rules = [
                'menu_name'                 => 'required',
                'menu_index'                => 'required',
                'menu_image'                => 'mimes:jpeg,jpg,png,gif|max:1024',
                // 'menu_descript'             => 'required'    

            ];
            $messages = [
                'menu_name.required'        => 'Please enter title',
                'menu_index.required'        => 'Please enter index',
                'menu_image.mimes' => 'Uploaded image is not in image format',
                'menu_image.max' => 'max size image 1Mb',
                // 'menu_descript.required'    => 'Please enter Description'
            ];
            $validator = Validator::make($request->all(),$rules,$messages);
            // if ($check_exist>0) {
            //         // $validator->after(function($validator){
            //         //     $validator->error()->add('menu_name.exists','This title already exists.Please enter another title');
            //         // });
            //         $request->session()->flash('error','This title already exists.Please enter another title');
            //         return redirect()->back();
            // }
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }else{

                    $image_list = "";
                    if($request->multi_image && !$request->multi_image_add)
                    {
                        $image_list = $request->multi_image;
                    }
                    if($request->multi_image_add && !$request->multi_image)
                    {
                        $image_list = implode(";",$request->multi_image_add);
                    }
                    if($request->multi_image_add  && $request->multi_image)
                    {
                        $image_list = $request->multi_image.";".implode(";",$request->multi_image_add);
                    }


                    if ($request->hasFile('menu_image')) {
                        
                        $images =  ImagesHelper::uploadImage($request->file('menu_image'),"menu",Session::get('place_ip_license'));
                    }else{
                        $images = $request->menu_image_old;
                    }

                $list_menu = PosMenu::where('menu_place_id',$this->getCurrentPlaceId())->get();
                if($menu_id>0){// UPDATE MENU
                    $imgs = PosMenu::where('menu_place_id',$this->getCurrentPlaceId())
                                ->where('menu_id',$menu_id)->first()->menu_list_image;
                                // dd($imgs);
                   
                   $PosMenu = PosMenu::where('menu_place_id',$this->getCurrentPlaceId())
                                ->where('menu_id',$menu_id)
                                ->update([
                                    'menu_name'         => $request->menu_name,
                                    'menu_parent_id'    => $request->menu_parent_id,
                                    'menu_url'          => $request->menu_url,
                                    'menu_index'        => $request->menu_index,
                                    'menu_image'        => $images,
                                    'menu_list_image'   => $image_list,
                                    'menu_descript'     => $menu_descript,
                                    'menu_type'         => $request->menu_type,
                                ]);
                        // dd($request->all());
                    // if($PosMenu){
                        $request->session()->flash('message','Edit Menu Success');
                    // }else{
                    //     $request->session()->flash('error','Edit Menu Error');
                    // }
                    // return view('webbuilder.menus',compact('list_menu'));
                    return redirect()->route("menus");
                }else{ // ADD NEW
                    $idPosMenu = PosMenu::where('menu_place_id',"=",$this->getCurrentPlaceId())->max('menu_id') +1;

                    $PosMenu = new PosMenu;
                                $PosMenu->menu_id           = $idPosMenu;
                                $PosMenu->menu_place_id     = $this->getCurrentPlaceId();
                                $PosMenu->menu_name         = $request->menu_name;
                                $PosMenu->menu_parent_id    = $request->menu_parent_id;
                                $PosMenu->menu_url          = $request->menu_url;
                                $PosMenu->menu_index        = $request->menu_index;
                                $PosMenu->menu_image        = $images;
                                $PosMenu->menu_list_image   = $image_list;
                                $PosMenu->menu_descript     = $menu_descript;
                                $PosMenu->menu_status       = 1;
                                $PosMenu->menu_type         = $request->menu_type;
                                $PosMenu->save();
                        if ($PosMenu) {
                            $request->session()->flash('message','Insert Menu Success');
                        }else{
                            $request->session()->flash('error','Insert Menu Error');
                        }
                    // return view('webbuilder.menus',compact('list_menu'));
                        return redirect()->route("menus");
                }
            }
    }
    
    public function deleteMenu(Request $request){
       $menu = PosMenu::where('menu_place_id',$this->getCurrentPlaceId())
                    ->where('menu_id',$request->id)
                    ->update(['menu_status'=> 0]);
        //dd($menu);
        if($menu){
            return "Delete Menu success";
        } else {
            return "Delete Menu Error";
        }
    }
     

    public function changeStatus(Request $request)
    {   
        $checked = $request->checked;      
        $menu_type=0;
        $menu_id = $request->id;
        
        if($checked == "checked"){
            $menu_type = 1;
        }
        PosMenu::where('menu_place_id',$this->getCurrentPlaceId())
                    ->where('menu_id',$menu_id)
                    ->update(['menu_type'=>$menu_type]);

        return "Update Status Success!";
    }

     public function uploadMultiImages(Request $request)
    {
        if ($request->hasFile('file')) {

                $imageFiles = $request->file('file');

                $image_name = [];

                foreach ($request->file('file') as $fileKey => $fileObject ) {

                    if ($fileObject->isValid()) {

                        $image_name[] = ImagesHelper::uploadImageDropZone($fileObject,'menu',$this->getCurrentPlaceIpLicense());
                    }
                }
                return $image_name;
            }
                return "upload error";
    }

    public function removeMenu(Request $request)
    {
        $menu_list_image = PosMenu::where('menu_place_id',$this->getCurrentPlaceId())
                 ->where('menu_id',$request->menu_id)
                 ->first()->menu_list_image;

        $menu_list_image = str_replace(";",",",$menu_list_image);

        $menu_list_image = explode(",",$menu_list_image);

        foreach (array_keys($menu_list_image, $request->src_image) as $key) {
                            unset($menu_list_image[$key]);
                        }
        $menu_list_image = implode(";", $menu_list_image);

        PosMenu::where('menu_place_id',$this->getCurrentPlaceId())
                 ->where('menu_id',$request->menu_id)
                 ->update(['menu_list_image'=>$menu_list_image]);

        return $menu_list_image;
    }

    public function get_importMenus(){
        return view('webbuilder.import_menus');
    }

    public function exportMenus(){

        $date = format_date(now());
        return \Excel::create('menus_table_'.$date,function($excel) {

            $excel ->sheet('Menus Table', function ($sheet) 
            {
                $sheet->cell('A1', function($cell) {$cell->setValue('Menu Name');   });
                $sheet->cell('B1', function($cell) {$cell->setValue('Menu Index');   });
                $sheet->cell('C1', function($cell) {$cell->setValue('Menu Url');   });
                $sheet->cell('D1', function($cell) {$cell->setValue('Menu Descript');   });
                $sheet->cell('E1', function($cell) {$cell->setValue('Menu Enable');   });
                // $sheet->cell('E1', function($cell) {$cell->setValue('Menu Image');   });
                // $sheet->cell('F1', function($cell) {$cell->setValue('Menu List Image');   });
                // $sheet->cell('G1', function($cell) {$cell->setValue('By');   });               

                
                        // $sheet->cell('A2', 'ex: HOME'); 
                        // $sheet->cell('B2', 'ex: 1'); 
                        // $sheet->cell('C2', 'ex: /home');
                        // $sheet->cell('D2', 'ex: home menu');
                        // $sheet->cell('E2', 'ex: /images/dsf87987dfs98d7f9sd8f/website/menu/1552381307download.jpg');
                        // $sheet->cell('F2', 'ex: /images/dsf87987dfs98d7f9sd8f/website/menu/city2_zing_jpg;/images/dsf87987dfs98d7f9sd8f/website/menu/download_jpg;/images/dsf87987dfs98d7f9sd8f/website/menu/city2_zing_jpg');
                        // $sheet->cell('G2', 'ex: luubatai123');
                       
                
            });
        })->download("xlsx");
    }

    public function post_importMenus(Request $request)
    {    
        try {
             if($request->hasFile('fileImport')){
                $path = $request->file('fileImport')->getRealPath();
                $data = \Excel::load($path)->toArray();
                $insert=0;
                $update=0;
                // dd($data);                
                foreach ($data as $value) {      

                        $check_menu_exist=PosMenu::where('menu_place_id',$this->getCurrentPlaceId())
                                                ->where('menu_name',$value['menu_name'])->count();

                                                // print_r($check_menu_exist);
                        if($check_menu_exist==0)                 
                        {
                            $arr = [];
                            $menu_id = PosMenu::where('menu_place_id',$this->getCurrentPlaceId())->max('menu_id')+1;
                            $arr['menu_id'] = $menu_id;
                            $arr['menu_place_id'] = $this->getCurrentPlaceId();
                            $arr['menu_name'] = $value['menu_name'];
                            $arr['menu_index'] = $value['menu_index'];
                            $arr['menu_url'] = $value['menu_url'];
                            $arr['menu_descript'] = $value['menu_descript']?$value['menu_descript']:"";
                            $arr['menu_type'] = $value['menu_enable']?$value['menu_enable']:1;

                            PosMenu::create($arr);
                            $insert++;
                        }
                        else
                        {
                            // return 1;
                            $arr = [];
                            $idmenu=PosMenu::where('menu_place_id',$this->getCurrentPlaceId())
                                                ->where('menu_name',$value['menu_name'])->first();
                            $menu_id=$idmenu->menu_id;
                            $arr['menu_id'] = $menu_id;
                            $arr['menu_place_id'] = $this->getCurrentPlaceId();
                            $arr['menu_name'] = $value['menu_name'];
                            $arr['menu_index'] = $value['menu_index'];
                            $arr['menu_url'] = $value['menu_url'];
                            $arr['menu_descript'] = $value['menu_descript']?$value['menu_descript']:"";
                            $arr['menu_type'] = $value['menu_enable']?$value['menu_enable']:1;
                            $arr['menu_status']    =1;

                            $p=PosMenu::where('menu_place_id',$this->getCurrentPlaceId())
                                                ->where('menu_id',$menu_id)
                                                ->update($arr);
                            $update++;
                        }
                        // // $arr['menu_image'] = $value['menu_image'];
                        // // $arr['menu_list_image'] = $value['menu_list_image'];
                        // // dd($arr);
                        // $menu_id++;
                    
                }
            }
            return redirect()->route('menus')->with('message',"Import Menus Success!, update: ".$update." row, inserted: ".$insert."row");     

        } catch (\Exception $e) {
            return back()->with('error','Import Menus Error!');           
        }           
        
    }
}
