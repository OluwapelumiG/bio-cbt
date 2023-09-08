<?php

namespace App\Http\Controllers;

use App\Models\Students;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentsController extends Controller
{
    //

    public function index(){
        $students = Students::get()->toArray();
        return view('students', ['students' => $students]);
    }

    public function new(Request $request){
        $request->validate([
            'matno' => 'required|string',
            'fname' => 'required|string',
            'lname' => 'required|string',
        ]);
        $student = new Students();

        $student->firstname = $request->fname;
        $student->middlename = $request->mname;
        $student->lastname = $request->lname;
        $student->matno = $request->matno;
        $student->save();

        return redirect()->route('students')->with('success', 'Student details added');
    }

    public function enroll(Request $request, $id){
        $student = Students::find($id)->first()->toArray();
        if ($student && count($student)>0){
            return view('enroll_student', ['student' => $student]);
        }
        else{
            redirect()->route('students')->with('error', 'Student not found');
        }
    }
    public function unenroll(Request $request){
        $student = Students::findOrFail($request->id);
        $student->facedata = null;
        $student->save();

        redirect()->route('students')->with('message', 'Student unenrolled');
    }

    public function save_face(Request $request){
        $student = Students::findOrFail($request->id);
        $student->facedata = $request->facedata;
        $student->save();

        return response()->json(['message' => 'Column updated successfully']);

    }

    public function attendance(){
        return view('attendance');
    }

    public function find_matno(Request $request){
        $student = Students::where('matno', $request->matno)->first();

        if ($student){
            $to = route('attendance.mark', $student->id);
            return response()->json(['message' => 'success', 'redirect' => $to]);
        }
        else{
            return response()->json(['message' => 'error']);
        }
    }

    public function mark_attendance(Request $request, $id){
        $student = Students::findOrFail($id)->first();

        if ($student) {
            return view('mark_student', ['student' => $student]);
        }
    }
}
