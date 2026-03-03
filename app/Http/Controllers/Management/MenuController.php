<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuActivityLog;
use Illuminate\Support\Facades\Auth;
class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::with('menus')->get();
        $menus = Menu::with('category')->orderBy('category_id')->orderBy('name')->get();
        return view ('management.menu')->with('menus',$menus)->with('categories', $categories);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories= Category::all();
        return view('management.createMenu')->with('categories',$categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|unique:menus|max:255',
            'price'=>'required|numeric|min:0',
             'category_id' =>  'required|numeric'

    ]);
    //if a user does not upload an image,use noimage.png for the menu
    $imageName= "noimage.png";
    //if a user upload a image
    if($request->image){
        $request->validate ([
            'image' => 'nullable|files|image|mimes:jpeg,png.jpg|max:5000'


        ]);
       $imageName= date('mdYHis').uniqid().'.'. $request->image->extension();
       $request->image->move(public_path('menu_images'),$imageName);
    }
    //save information to menu table
    $menu = new Menu();
    $menu->name = $request ->name;
    $menu->price = $request-> price;
    $menu->image = $imageName;
    $menu->description = $request->description;
    $menu->category_id =$request->category_id;
    $menu->save();
    
    // Log activity
    MenuActivityLog::create([
        'user_id' => Auth::id(),
        'user_name' => Auth::user()->name,
        'menu_id' => $menu->id,
        'menu_name' => $menu->name,
        'action' => 'Create',
        'details' => 'Created menu: ' . $menu->name,
        'old_price' => null,
        'new_price' => $menu->price
    ]);
    
    $request->session()->flash('status',$request->name. ' is saved successfully');
    return redirect('/management/menu');
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $menu = Menu::find($id);
        $categories = Category::all();
        return view('management.editMenu')->with('menu',$menu)->with('categories',$categories);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //information validation
        $request->validate([
            'name'=>'required|max:255',
            'price'=>'required|numeric|min:0',
            'category_id'=>'required|numeric'
        ]);
        $menu = Menu::find($id);
        
        // Store old values for logging
        $oldPrice = $menu->price;
        $oldName = $menu->name;
        
        //validate if a user upload a image
        if($request->image){
            $request->validate([
                'image'=>'nullable|file|image|mimes:jpeg,png,jpp|max:5000'
            ]);

            if($menu->image !="noimage.png"){
                $imageName=$menu->image;
                unlink(public_path('menu_images').'/'.$imageName);

            }
            $imageName =date('mdYHis').uniqid().'.'.$request->image->extension();
        $request->image->move(public_path('menu_images'),$imageName);
        } else { $imageName = $menu->image;

        }
        $menu->name = $request->name;
        $menu->price = $request ->price;
        $menu->image=$imageName;
        $menu->description = $request->description;
        $menu->category_id = $request->category_id;
        $menu->save();
        
        // Log activity - especially price changes
        $details = '';
        if ($oldPrice != $menu->price) {
            $details = 'Updated menu: ' . $oldName . ' | Price changed from Rs ' . number_format($oldPrice, 2) . ' to Rs ' . number_format($menu->price, 2);
        } else {
            $details = 'Updated menu: ' . $oldName;
        }
        
        MenuActivityLog::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'menu_id' => $menu->id,
            'menu_name' => $menu->name,
            'action' => 'Update',
            'details' => $details,
            'old_price' => $oldPrice,
            'new_price' => $menu->price
        ]);
        
        $request -> session ()->flash('status',$request->name. ' is updated succesfully');
        return redirect('/management/menu');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $menu=Menu::find($id);
        if ($menu->image !="noimage.png"){
            unlink(public_path('menu_images').'/'.$menu->image);

        }
        $menuName=$menu->name;
        $menuPrice = $menu->price;
        
        // Log activity before deletion
        MenuActivityLog::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'menu_id' => $menu->id,
            'menu_name' => $menuName,
            'action' => 'Delete',
            'details' => 'Deleted menu: ' . $menuName . ' (Rs ' . number_format($menuPrice, 2) . ')',
            'old_price' => $menuPrice,
            'new_price' => null
        ]);
        
        $menu->delete();
        Session()->flash('status',$menuName. ' is deleted Successfully');
         return redirect('management/menu');
        
        ;
    }
    
    /**
     * Display menu activity logs
     *
     * @return \Illuminate\Http\Response
     */
    public function activityLog(Request $request)
    {
        $query = MenuActivityLog::with('user')->orderBy('created_at', 'desc');
        
        // Filter by action if provided
        if ($request->has('action') && $request->action != '') {
            $query->where('action', $request->action);
        }
        
        // Filter by date if provided
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('created_at', $request->date);
        }
        
        // Filter by user if provided
        if ($request->has('user') && $request->user != '') {
            $query->where('user_name', 'like', '%' . $request->user . '%');
        }
        
        $logs = $query->paginate(50);
        
        return view('management.menuActivityLog')->with('logs', $logs);
    }
}
