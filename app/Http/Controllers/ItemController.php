<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use DB;
use App\Events\Notifications;
use DataTables;

class ItemController extends Controller
{
    public function __construct()
        {
        $this->middleware("auth");
        $this->middleware("canView:items,write", [
        'only' => [
            'create' ,
            'store' ,
            'edit' ,
            'update' ,
            ]
        ]);
        $this->middleware("canView:items,read", [
        'only' => [
            'index' ,
            ]
        ]);
        $this->middleware("canView:items,delete", [
        'only' => [
            'destroy' ,
            ]
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('items.report');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $units =  DB::table('units')
                ->select('*')
                ->get();
        $types =  DB::table('types')
                ->select('*')
                ->whereIn('store', array(1, auth()->user()->store))
                ->get();
        if(auth()->user()->store ==1){
            $stores =  DB::table('stores')
                ->select('*')
                ->get();
        }else{
            $stores =  DB::table('stores')
                ->select('*')
                ->where('id',  auth()->user()->store )
                ->get();
        }
        
        return view('items.create',[
            'units' =>$units,
            'types' =>$types,
            'stores' =>$stores
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'code' => ['required', 'max:255' , 'unique:items'],
            'name' => ['required','max:255', 'unique:items'],
            'balance' => ['required'],
            'unit' => ['required'],
            'price' => ['required'],
            'end' => ['required'],
            'type' => ['required'],
            'store' => ['required'],
            ],
            [
                'code.unique' => '   ???????????? ??????????   ??????????  ',
                'code.required' => '  ????????    ???????????? ??????????   ',

                'name.unique' => ' ???????????????? ??????????    ??????????  ',
                'name.required' => ' ???????? ???????????????? ?????????? ',

                'balance.required' => '  ????????    ???????????? ????????????   ',
                'unit.required' => '  ????????  ????????????    ',
                'price.required' => '  ????????  ?????? ????????????   ',
                'end.required' => '  ????????  ???? ??????????     ',
                'type.required' => '  ????????   ?????? ??????????   ',
                'store.required' => '  ????????    ????????????   ',
                
            ]);
            $item = new Item;
    
            $item->code =$request->code;
            $item->name = $request->name;
            $item->store = $request->store;
            $item->first = $request->balance;
            $item->balance = $request->balance;
            $item->unit = $request->unit;
            $item->price = $request->price;
            $item->end = $request->end;
            $item->type = $request->type;
            $item->notes = $request->notes;
            $item->save();

            $store = DB::table('stores')->select('*')->where('id' ,$request->store)->first();
            $unit = DB::table('units')->select('*')->where('id' ,$request->unit)->first();

            $title = '????   ?????????? ?????? ???????? ';
            $body =  '  ???? ?????????? ?????? ????????  ??????  '.$request->code;$body.= "\r\n /";
            $body .=  '??????   '.$request->name;$body.= "\r\n /";
            $body .=  '    '.$store->name;$body.= "\r\n /";
            $body .=  ' ????????  '.$request->balance;$body.= "\r\n /";
            $body .=  ' ????????  '.$unit->name;$body.= "\r\n /";
            $body .=  ' ??????  '.$request->price.'  ?????????? ';$body.= "\r\n /";

            $request->session()->flash('NewItem', $title);
            
            $auth = new AuthController();
            $auth->notify(auth()->user()->id, $request->store, $title, $body, '/items', 'action');

            event(new Notifications($title));

            return  back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        $new = 0 ;
        $ins =   DB::table('ins')->select('*')->where([['item', $item->id]])->first();
        $outs =   DB::table('ins')->select('*')->where([['item', $item->id]])->first();
        if($ins != null or $outs != null) $new = 1 ;

        $units =  DB::table('units')
                ->select('*')
                ->get();
        $types =  DB::table('types')
                ->select('*')
                ->whereIn('store', array(1, auth()->user()->store))
                ->get();
        if(auth()->user()->store ==1){
            $stores =  DB::table('stores')
                ->select('*')
                ->get();
        }else{
            $stores =  DB::table('stores')
                ->select('*')
                ->where('id',  auth()->user()->store )
                ->get();
        }
        
        return view('items.edit',[
            'new' => $new,
            'item' => $item,
            'units' =>$units,
            'types' =>$types,
            'stores' =>$stores
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item)
    {
        $new = 0 ;
        $ins =   DB::table('ins')->select('*')->where([['item', $item->id]])->first();
        $outs =   DB::table('ins')->select('*')->where([['item', $item->id]])->first();
        if($ins != null or $outs != null) $new = 1 ;

        $units =  DB::table('units')
                ->select('*')
                ->get();
        $types =  DB::table('types')
                ->select('*')
                ->whereIn('store', array(1, auth()->user()->store))
                ->get();
        if(auth()->user()->store ==1){
            $stores =  DB::table('stores')
                ->select('*')
                ->get();
        }else{
            $stores =  DB::table('stores')
                ->select('*')
                ->where('id',  auth()->user()->store )
                ->get();
        }

        $body = '';
        $validatedData = $request->validate([
            'code' => ['required', 'max:255' ,  'unique:items,code,'.$item->id],
            'name' => ['required','max:255', 'unique:items,name,'.$item->id],
            'balance' => ['required'],
            'unit' => ['required'],
            'price' => ['required'],
            'end' => ['required'],
            'type' => ['required'],
            'store' => ['required'],
            ],
            [
                'code.unique' => '   ???????????? ??????????   ??????????  ',
                'code.required' => '  ????????    ???????????? ??????????   ',

                'name.unique' => ' ???????????????? ??????????    ??????????  ',
                'name.required' => ' ???????? ???????????????? ?????????? ',

                'balance.required' => '  ????????    ???????????? ????????????   ',
                'unit.required' => '  ????????  ????????????    ',
                'price.required' => '  ????????  ?????? ????????????   ',
                'end.required' => '  ????????  ???? ??????????     ',
                'type.required' => '  ????????   ?????? ??????????   ',
                'store.required' => '  ????????    ????????????   ',
                
            ]);


            if($item->code != $request->code){
                    $body .=  '  ???? ?????????? ?????? ?????????? ???? '.$item->code. ' ?????? '.$request->code;
                    $body.= "\r\n /";
            }   
            if($item->name != $request->name) {
                $body .=  '  ???? ?????????? ?????? ?????????? ???? ' . $item->name. ' ?????? '.$request->name;
                $body.= "\r\n /";
            }
            if( $new == 0 ){
            if($item->balance != $request->balance) {
                $body .=  '  ???? ?????????? ???????????? ????????????  ???? ' . $item->balance. ' ?????? '.$request->balance;
                $body.= "\r\n /";
            }}
            if($item->unit != $request->unit) {
                $unit1 =  DB::table('units')->select('*')->where('id', $item->unit)->first();
                $unit2 =  DB::table('units')->select('*')->where('id', $request->unit)->first();
                    
                $body .=  '  ???? ?????????? ????????????  ???? ' . $unit1->name. ' ?????? '.$unit2->name;
                $body.= "\r\n /";
            }
            if( $new == 0 ){
            if($item->price != $request->price) {
                $body .=  '  ???? ?????????? ?????? ????????????  ???? ' . $item->price. ' ?????? '.$request->price;
                $body.= "\r\n /";
            }}
            if($item->end != $request->end) {
                $body .=  '  ???? ?????????? ???? ??????????  ???? ' . $item->end. ' ?????? '.$request->end;
                $body.= "\r\n /";
            }
            if($item->type != $request->type) {
                $type1 =  DB::table('types')->select('*')->where('id', $item->type)->first();
                $type2 =  DB::table('types')->select('*')->where('id', $request->type)->first();

                $body .=  '  ???? ?????????? ?????? ?????????? ???? ' . $type1->name. ' ?????? '.$type2->name;
                $body.= "\r\n /";
            }
            
            $item->code = $request->code;
            $item->name = $request->name;
            if( $new == 0 )$item->balance = $request->balance;
            if( $new == 0 )$item->first = $request->balance;
            $item->unit = $request->unit;
            if( $new == 0 )$item->price = $request->price;
            $item->end = $request->end;
            $item->type = $request->type;
            $item->store = $request->store;
            $item->notes = $request->notes;
            
            $item->save();
            $title = '???? ?????????? ?????????? ??????????';


            $request->session()->flash('EditItem', $title);
            $auth = new AuthController();

            if($body != null)$auth->notify(auth()->user()->id, auth()->user()->store, $title, $body, '/items', 'action');

            event(new Notifications($title));

        return  back()->with([
            'item' => $item,
            'units' =>$units,
            'types' =>$types,
            'stores' =>$stores
        ]);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
            $item->is_delete = 1;
            $item->save();

            $title = '???? ?????? ?????????? ??????????';


            $request->session()->flash('DeleteItem', $title);
            $auth = new AuthController();

            $store = DB::table('stores')->select('*')->where('id' ,$item->store)->first();
            $unit = DB::table('units')->select('*')->where('id' ,$item->unit)->first();

            $body =  '  ???? ?????? ??????   ??????  '.$item->code;$body.= "\r\n /";
            $body .=  '??????   '.$item->name;$body.= "\r\n /";
            $body .=  '    '.$store->name;$body.= "\r\n /";
            $body .=  ' ????????  '.$item->balance;$body.= "\r\n /";
            $body .=  ' ????????  '.$unit->name;$body.= "\r\n /";
            $body .=  ' ??????  '.$item->price.'  ?????????? ';$body.= "\r\n /";

            if($body != null)$auth->notify(auth()->user()->id, auth()->user()->store, $title, $body, '/items', 'action');

            event(new Notifications($title));

            return response($item, 201);
    }

    public function itemsData()
    {
            $items = Item::whereIn('store', array(1, auth()->user()->store))->where('is_delete', 0)->get();
                        
            foreach ($items as  $item) {
                    $unit =  DB::table('units')->select('*')->where('id', $item->unit)->first();
                    $type =  DB::table('types')->select('*')->where('id', $item->type)->first();

                    $item->unit   = $unit->name;
                    $item->type   = $type->name;
                }
            return DataTables::of($items) 
                ->addColumn('action', function ($item) {
                    return '<a  href="/items/' . $item->id . '/edit" class="edit-button"><i class="fas fa-edit"></i> </a>
                    <a  coords="' . $item->name . '"  id="' . $item->id . '" onclick="getId(this.id, this.coords)"  href="#" class="delete-button"><i class="fas fa-trash-alt"></i> </a>';
            })->make(true);
            
    }
     public function dataAjax(Request $request)
    {

        $search = $request->search;

      if($search == ''){
         $items = Item::orderby('name','asc')->select('*')->whereIn('store', array(1, auth()->user()->store))->limit(10)->get();
      }else{
         $items = Item::orderby('name','asc')->select('*')->where('name', 'like', '%' .$search . '%')->whereIn('store', array(1, auth()->user()->store))->limit(10)->get();
      }

      $response = array();
      foreach($items as $item){
         $response[] = array(
              "id"=>$item->id,
              "text"=> ' ?????? '.$item->balance.' - '.$item->name
         );
      }
      return response()->json($response); 
    }


}
