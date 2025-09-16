<?php<?php<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;namespace App\Http\Controllers;namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use App\Models\FacultySemesterCompliance;

use App\Models\SubjectCompliance;

use App\Models\User;use Illuminate\Http\Request;use Illuminate\Http\Request;



class ComplianceActionController extends Controlleruse Illuminate\Support\Facades\Auth;use Illuminate\Support\Facades\Auth;

{

    /**use App\Models\FacultySemesterCompliance;use App\Models\FacultySemesterCompliance;

     * Approve semester compliance submission

     */use App\Models\SubjectCompliance;use App\Models\SubjectCompliance;

    public function approveSemesterCompliance(Request $request, $id)

    {use App\Models\User;

        $user = Auth::user();

        $compliance = FacultySemesterCompliance::findOrFail($id);class ComplianceActionController extends Controller

        

        // Check if user has permission to approveclass ComplianceActionController extends Controller{

        if (!$this->canApprove($user, $compliance)) {

            abort(403, 'Unauthorized to approve this submission');{    /**

        }

            /**     * Approve semester compliance by Program Head

        // Update approval status based on user role

        if ($user->role->name === 'Program Head') {     * Approve semester compliance submission     */

            $compliance->program_head_approval_status = 'approved';

            $compliance->program_head_approved_by = $user->id;     */    public function approveSemesterCompliance(Request $request, $complianceId)

            $compliance->program_head_approved_at = now();

        } elseif ($user->role->name === 'Dean') {    public function approveSemesterCompliance(Request $request, $id)    {

            $compliance->dean_approval_status = 'approved';

            $compliance->dean_approved_by = $user->id;    {        $user = Auth::user();

            $compliance->dean_approved_at = now();

        }        $user = Auth::user();        

        

        // Update overall approval status        $compliance = FacultySemesterCompliance::findOrFail($id);        if (!in_array($user->role->name, ['Program Head', 'Dean'])) {

        $this->updateOverallApprovalStatus($compliance);

        $compliance->save();                    return response()->json(['error' => 'Unauthorized'], 403);

        

        return back()->with('success', 'Compliance submission approved successfully.');        // Check if user has permission to approve        }

    }

            if (!$this->canApprove($user, $compliance)) {        

    /**

     * Reject semester compliance submission (needs revision)            abort(403, 'Unauthorized to approve this submission');        $compliance = FacultySemesterCompliance::findOrFail($complianceId);

     */

    public function rejectSemesterCompliance(Request $request, $id)        }        

    {

        $request->validate([                // Verify the faculty belongs to the user's program (for Program Heads)

            'rejection_reason' => 'required|string|max:500'

        ]);        // Update approval status based on user role        if ($user->role->name === 'Program Head') {

        

        $user = Auth::user();        if ($user->role->name === 'Program Head') {            $facultyProgramIds = $compliance->user->facultyAssignments()->pluck('program_id')->toArray();

        $compliance = FacultySemesterCompliance::findOrFail($id);

                    $compliance->program_head_approval_status = 'approved';            

        // Check if user has permission to reject

        if (!$this->canApprove($user, $compliance)) {            $compliance->program_head_approved_by = $user->id;            if (!in_array($user->program_id, $facultyProgramIds)) {

            abort(403, 'Unauthorized to reject this submission');

        }            $compliance->program_head_approved_at = now();                return response()->json(['error' => 'Unauthorized access to this compliance record'], 403);

        

        // Update approval status based on user role        } elseif ($user->role->name === 'Dean') {            }

        if ($user->role->name === 'Program Head') {

            $compliance->program_head_approval_status = 'needs_revision';            $compliance->dean_approval_status = 'approved';            

            $compliance->program_head_approved_by = $user->id;

            $compliance->program_head_approved_at = now();            $compliance->dean_approved_by = $user->id;            // Program Head approval

        } elseif ($user->role->name === 'Dean') {

            $compliance->dean_approval_status = 'needs_revision';            $compliance->dean_approved_at = now();            $compliance->update([

            $compliance->dean_approved_by = $user->id;

            $compliance->dean_approved_at = now();        }                'program_head_approval_status' => 'approved',

        }

                                'program_head_approved_by' => $user->id,

        $compliance->rejection_reason = $request->rejection_reason;

        $compliance->approval_status = 'needs_revision';        // Update overall approval status                'program_head_approved_at' => now(),

        $compliance->save();

                $this->updateOverallApprovalStatus($compliance);                'program_head_comments' => $request->input('comments', '')

        return back()->with('warning', 'Compliance submission marked as needs revision.');

    }        $compliance->save();            ]);

    

    /**                    

     * Approve subject compliance submission

     */        return back()->with('success', 'Compliance submission approved successfully.');            return response()->json([

    public function approveSubjectCompliance(Request $request, $id)

    {    }                'success' => true,

        $user = Auth::user();

        $compliance = SubjectCompliance::findOrFail($id);                    'message' => 'Compliance approved by Program Head',

        

        // Check if user has permission to approve    /**                'status' => 'approved_by_program_head'

        if (!$this->canApproveSubject($user, $compliance)) {

            abort(403, 'Unauthorized to approve this submission');     * Reject semester compliance submission (needs revision)            ]);

        }

             */        }

        // Update approval status based on user role

        if ($user->role->name === 'Program Head') {    public function rejectSemesterCompliance(Request $request, $id)        

            $compliance->program_head_approval_status = 'approved';

            $compliance->program_head_approved_by = $user->id;    {        // Dean approval (can only approve if Program Head has approved)

            $compliance->program_head_approved_at = now();

        } elseif ($user->role->name === 'Dean') {        $request->validate([        if ($user->role->name === 'Dean') {

            $compliance->dean_approval_status = 'approved';

            $compliance->dean_approved_by = $user->id;            'rejection_reason' => 'required|string|max:500'            // Check if Program Head has approved first

            $compliance->dean_approved_at = now();

        }        ]);            if ($compliance->program_head_approval_status !== 'approved') {

        

        // Update overall approval status                        return response()->json(['error' => 'Program Head must approve first'], 400);

        $this->updateOverallSubjectApprovalStatus($compliance);

        $compliance->save();        $user = Auth::user();            }

        

        return back()->with('success', 'Subject compliance approved successfully.');        $compliance = FacultySemesterCompliance::findOrFail($id);            

    }

                        $compliance->update([

    /**

     * Reject subject compliance submission (needs revision)        // Check if user has permission to reject                'dean_approval_status' => 'approved',

     */

    public function rejectSubjectCompliance(Request $request, $id)        if (!$this->canApprove($user, $compliance)) {                'dean_approved_by' => $user->id,

    {

        $request->validate([            abort(403, 'Unauthorized to reject this submission');                'dean_approved_at' => now(),

            'rejection_reason' => 'required|string|max:500'

        ]);        }                'dean_comments' => $request->input('comments', ''),

        

        $user = Auth::user();                        // Update overall approval status only when Dean approves

        $compliance = SubjectCompliance::findOrFail($id);

                // Update approval status based on user role                'approval_status' => 'approved',

        // Check if user has permission to reject

        if (!$this->canApproveSubject($user, $compliance)) {        if ($user->role->name === 'Program Head') {                'approved_by' => $user->id,

            abort(403, 'Unauthorized to reject this submission');

        }            $compliance->program_head_approval_status = 'needs_revision';                'approved_at' => now(),

        

        // Update approval status based on user role            $compliance->program_head_approved_by = $user->id;            ]);

        if ($user->role->name === 'Program Head') {

            $compliance->program_head_approval_status = 'needs_revision';            $compliance->program_head_approved_at = now();            

            $compliance->program_head_approved_by = $user->id;

            $compliance->program_head_approved_at = now();        } elseif ($user->role->name === 'Dean') {            return response()->json([

        } elseif ($user->role->name === 'Dean') {

            $compliance->dean_approval_status = 'needs_revision';            $compliance->dean_approval_status = 'needs_revision';                'success' => true,

            $compliance->dean_approved_by = $user->id;

            $compliance->dean_approved_at = now();            $compliance->dean_approved_by = $user->id;                'message' => 'Compliance fully approved by Dean',

        }

                    $compliance->dean_approved_at = now();                'status' => 'fully_approved'

        $compliance->rejection_reason = $request->rejection_reason;

        $compliance->approval_status = 'needs_revision';        }            ]);

        $compliance->save();

                        }

        return back()->with('warning', 'Subject compliance marked as needs revision.');

    }        $compliance->rejection_reason = $request->rejection_reason;        

    

    /**        $compliance->approval_status = 'needs_revision';        return response()->json(['error' => 'Unauthorized'], 403);

     * Check if user can approve semester compliance

     */        $compliance->save();    }

    private function canApprove(User $user, FacultySemesterCompliance $compliance)

    {            

        if ($user->role->name === 'Program Head') {

            // Program head can approve if submission is from their program        return back()->with('warning', 'Compliance submission marked as needs revision.');    /**

            return $compliance->user->program_id === $user->program_id;

        }    }     * Request revision for semester compliance

        

        if ($user->role->name === 'Dean') {         */

            // Dean can approve if submission is from their department

            return $compliance->user->department_id === $user->department_id;    /**    public function rejectSemesterCompliance(Request $request, $complianceId)

        }

             * Approve subject compliance submission    {

        return false;

    }     */        $user = Auth::user();

    

    /**    public function approveSubjectCompliance(Request $request, $id)        

     * Check if user can approve subject compliance

     */    {        if (!in_array($user->role->name, ['Program Head', 'Dean'])) {

    private function canApproveSubject(User $user, SubjectCompliance $compliance)

    {        $user = Auth::user();            return response()->json(['error' => 'Unauthorized'], 403);

        if ($user->role->name === 'Program Head') {

            // Program head can approve if subject is from their program        $compliance = SubjectCompliance::findOrFail($id);        }

            return $compliance->subject->program_id === $user->program_id;

        }                

        

        if ($user->role->name === 'Dean') {        // Check if user has permission to approve        $compliance = FacultySemesterCompliance::findOrFail($complianceId);

            // Dean can approve if subject is from their department

            return $compliance->subject->program->department_id === $user->department_id;        if (!$this->canApproveSubject($user, $compliance)) {        

        }

                    abort(403, 'Unauthorized to approve this submission');        if ($user->role->name === 'Program Head') {

        return false;

    }        }            // Verify the faculty belongs to the program head's program

    

    /**                    $facultyProgramIds = $compliance->user->facultyAssignments()->pluck('program_id')->toArray();

     * Update overall approval status for semester compliance

     */        // Update approval status based on user role            

    private function updateOverallApprovalStatus(FacultySemesterCompliance $compliance)

    {        if ($user->role->name === 'Program Head') {            if (!in_array($user->program_id, $facultyProgramIds)) {

        if ($compliance->program_head_approval_status === 'approved' && 

            $compliance->dean_approval_status === 'approved') {            $compliance->program_head_approval_status = 'approved';                return response()->json(['error' => 'Unauthorized access to this compliance record'], 403);

            $compliance->approval_status = 'approved';

        } elseif ($compliance->program_head_approval_status === 'needs_revision' ||             $compliance->program_head_approved_by = $user->id;            }

                  $compliance->dean_approval_status === 'needs_revision') {

            $compliance->approval_status = 'needs_revision';            $compliance->program_head_approved_at = now();            

        } else {

            $compliance->approval_status = 'pending';        } elseif ($user->role->name === 'Dean') {            // Program Head requests revision

        }

    }            $compliance->dean_approval_status = 'approved';            $compliance->update([

    

    /**            $compliance->dean_approved_by = $user->id;                'program_head_approval_status' => 'needs_revision',

     * Update overall approval status for subject compliance

     */            $compliance->dean_approved_at = now();                'program_head_approved_by' => $user->id,

    private function updateOverallSubjectApprovalStatus(SubjectCompliance $compliance)

    {        }                'program_head_approved_at' => now(),

        if ($compliance->program_head_approval_status === 'approved' && 

            $compliance->dean_approval_status === 'approved') {                        'program_head_comments' => $request->input('comments', ''),

            $compliance->approval_status = 'approved';

        } elseif ($compliance->program_head_approval_status === 'needs_revision' ||         // Update overall approval status                // Reset overall approval status

                  $compliance->dean_approval_status === 'needs_revision') {

            $compliance->approval_status = 'needs_revision';        $this->updateOverallSubjectApprovalStatus($compliance);                'approval_status' => 'needs_revision',

        } else {

            $compliance->approval_status = 'pending';        $compliance->save();                'approved_by' => $user->id,

        }

    }                        'approved_at' => now(),

}
        return back()->with('success', 'Subject compliance approved successfully.');            ]);

    }            

                return response()->json([

    /**                'success' => true,

     * Reject subject compliance submission (needs revision)                'message' => 'Compliance marked for revision by Program Head',

     */                'status' => 'needs_revision'

    public function rejectSubjectCompliance(Request $request, $id)            ]);

    {        }

        $request->validate([        

            'rejection_reason' => 'required|string|max:500'        if ($user->role->name === 'Dean') {

        ]);            // Dean can request revision regardless of Program Head status

                    $compliance->update([

        $user = Auth::user();                'dean_approval_status' => 'needs_revision',

        $compliance = SubjectCompliance::findOrFail($id);                'dean_approved_by' => $user->id,

                        'dean_approved_at' => now(),

        // Check if user has permission to reject                'dean_comments' => $request->input('comments', ''),

        if (!$this->canApproveSubject($user, $compliance)) {                // Reset overall approval status

            abort(403, 'Unauthorized to reject this submission');                'approval_status' => 'needs_revision',

        }                'approved_by' => $user->id,

                        'approved_at' => now(),

        // Update approval status based on user role            ]);

        if ($user->role->name === 'Program Head') {            

            $compliance->program_head_approval_status = 'needs_revision';            return response()->json([

            $compliance->program_head_approved_by = $user->id;                'success' => true,

            $compliance->program_head_approved_at = now();                'message' => 'Compliance marked for revision by Dean',

        } elseif ($user->role->name === 'Dean') {                'status' => 'needs_revision'

            $compliance->dean_approval_status = 'needs_revision';            ]);

            $compliance->dean_approved_by = $user->id;        }

            $compliance->dean_approved_at = now();        

        }        return response()->json(['error' => 'Unauthorized'], 403);

            }

        $compliance->rejection_reason = $request->rejection_reason;    

        $compliance->approval_status = 'needs_revision';    /**

        $compliance->save();     * Approve subject compliance

             */

        return back()->with('warning', 'Subject compliance marked as needs revision.');    public function approveSubjectCompliance(Request $request, $complianceId)

    }    {

            $user = Auth::user();

    /**        

     * Check if user can approve semester compliance        if (!in_array($user->role->name, ['Program Head', 'Dean'])) {

     */            return response()->json(['error' => 'Unauthorized'], 403);

    private function canApprove(User $user, FacultySemesterCompliance $compliance)        }

    {        

        if ($user->role->name === 'Program Head') {        $compliance = SubjectCompliance::findOrFail($complianceId);

            // Program head can approve if submission is from their program        

            return $compliance->user->program_id === $user->program_id;        if ($user->role->name === 'Program Head') {

        }            // Verify the subject belongs to the program head's program

                    $subject = $compliance->subject;

        if ($user->role->name === 'Dean') {            

            // Dean can approve if submission is from their department            if ($subject->program_id !== $user->program_id) {

            return $compliance->user->department_id === $user->department_id;                return response()->json(['error' => 'Unauthorized access to this subject compliance record'], 403);

        }            }

                    

        return false;            // Program Head approval (first level)

    }            $compliance->update([

                    'program_head_approval_status' => 'approved',

    /**                'program_head_approved_by' => $user->id,

     * Check if user can approve subject compliance                'program_head_approved_at' => now(),

     */                'program_head_comments' => $request->input('comments', ''),

    private function canApproveSubject(User $user, SubjectCompliance $compliance)            ]);

    {            

        if ($user->role->name === 'Program Head') {            return response()->json([

            // Program head can approve if subject is from their program                'success' => true,

            return $compliance->subject->program_id === $user->program_id;                'message' => 'Subject compliance approved by Program Head. Awaiting Dean approval for final approval.',

        }                'status' => 'approved_by_program_head'

                    ]);

        if ($user->role->name === 'Dean') {        }

            // Dean can approve if subject is from their department        

            return $compliance->subject->program->department_id === $user->department_id;        if ($user->role->name === 'Dean') {

        }            // Dean can only approve if Program Head has approved first

                    if ($compliance->program_head_approval_status !== 'approved') {

        return false;                return response()->json([

    }                    'error' => 'Subject compliance must be approved by Program Head first',

                        'current_status' => $compliance->program_head_approval_status

    /**                ], 422);

     * Update overall approval status for semester compliance            }

     */            

    private function updateOverallApprovalStatus(FacultySemesterCompliance $compliance)            // Dean approval (final approval)

    {            $compliance->update([

        if ($compliance->program_head_approval_status === 'approved' &&                 'dean_approval_status' => 'approved',

            $compliance->dean_approval_status === 'approved') {                'dean_approved_by' => $user->id,

            $compliance->approval_status = 'approved';                'dean_approved_at' => now(),

        } elseif ($compliance->program_head_approval_status === 'needs_revision' ||                 'dean_comments' => $request->input('comments', ''),

                  $compliance->dean_approval_status === 'needs_revision') {                // Set final approval status

            $compliance->approval_status = 'needs_revision';                'approval_status' => 'approved',

        } else {                'approved_by' => $user->id,

            $compliance->approval_status = 'pending';                'approved_at' => now(),

        }            ]);

    }            

                return response()->json([

    /**                'success' => true,

     * Update overall approval status for subject compliance                'message' => 'Subject compliance fully approved by Dean',

     */                'status' => 'approved'

    private function updateOverallSubjectApprovalStatus(SubjectCompliance $compliance)            ]);

    {        }

        if ($compliance->program_head_approval_status === 'approved' &&         

            $compliance->dean_approval_status === 'approved') {        return response()->json(['error' => 'Unauthorized'], 403);

            $compliance->approval_status = 'approved';    }

        } elseif ($compliance->program_head_approval_status === 'needs_revision' ||     

                  $compliance->dean_approval_status === 'needs_revision') {    /**

            $compliance->approval_status = 'needs_revision';     * Request revision for subject compliance

        } else {     */

            $compliance->approval_status = 'pending';    public function rejectSubjectCompliance(Request $request, $complianceId)

        }    {

    }        $user = Auth::user();

}        
        if (!in_array($user->role->name, ['Program Head', 'Dean'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $compliance = SubjectCompliance::findOrFail($complianceId);
        
        if ($user->role->name === 'Program Head') {
            // Verify the subject belongs to the program head's program
            $subject = $compliance->subject;
            
            if ($subject->program_id !== $user->program_id) {
                return response()->json(['error' => 'Unauthorized access to this subject compliance record'], 403);
            }
            
            // Program Head requests revision
            $compliance->update([
                'program_head_approval_status' => 'needs_revision',
                'program_head_approved_by' => $user->id,
                'program_head_approved_at' => now(),
                'program_head_comments' => $request->input('comments', ''),
                // Reset overall approval status
                'approval_status' => 'needs_revision',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Subject compliance marked for revision by Program Head',
                'status' => 'needs_revision'
            ]);
        }
        
        if ($user->role->name === 'Dean') {
            // Dean can request revision regardless of Program Head status
            $compliance->update([
                'dean_approval_status' => 'needs_revision',
                'dean_approved_by' => $user->id,
                'dean_approved_at' => now(),
                'dean_comments' => $request->input('comments', ''),
                // Reset overall approval status
                'approval_status' => 'needs_revision',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Subject compliance marked for revision by Dean',
                'status' => 'needs_revision'
            ]);
        }
        
        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
