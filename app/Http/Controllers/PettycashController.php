<?php

namespace App\Http\Controllers;
use App\Models\PettycashTrans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PettycashController extends Controller
{

    public function index(Request $request)
    {
        if($request->date)
        {
            $trans = PettycashTrans::where('trans_date', $request->date)->orderBy('id','desc')->paginate(100);




            
        }
        else
        {
            $trans = PettycashTrans::orderBy('id','desc')->paginate(100);
        }
        
        $summeries = DB::select('
                                SELECT REPLACE(pt.TypeOfTrans, "_", " ") AS TypeOfTrans,
                                (SELECT SUM(Amount) FROM pettycash_trans WHERE TypeOfTrans = pt.TypeOfTrans GROUP BY TypeOfTrans) AS total
                                FROM pettycash_trans pt
                                GROUP BY pt.TypeOfTrans
                            ');
        return view('pettycash.index', compact('trans', 'summeries'))
        ->with('ggg', $trans->sum('Amount'));
        
    }

    public function store(Request $request)
    {
        $request->validate([
            'trans_date' => 'required',
            'TypeOfTrans' => 'required',
            'Employee' => 'required',
            'Description' => 'required',
            'Amount' => 'required',
        ]);
        
        if($request->tran_id)
        {
            PettycashTrans::where('id', $request->tran_id)
                            ->update([
                                'trans_date' => $request->trans_date,
                                'TypeOfTrans' => $request->TypeOfTrans,
                                'Employee' => $request->Employee,
                                'Description' => $request->Description,
                                'Amount' => $request->Amount
                            ]);
        }
        else
        {
            PettycashTrans::create($request->post());
        }

        return redirect()->route('pettycash')->with('success','Cash has been added successfully.');
    }

    public function destroy($id)
    {
        DB::table('pettycash_trans')->delete($id);
        return redirect()->route('pettycash')->with('success','Company has been deleted successfully');
    }

}
