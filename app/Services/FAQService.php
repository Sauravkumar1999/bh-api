<?php
namespace App\Services;

use App\Http\Requests\FAQRequest;
use App\Models\FAQ;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\FAQResource;

class FAQService 
{

  public function createFAQ($request)
  {
    try {
      $validatedData = $request->validated();
      $data = array_merge($validatedData, ['user_id' => Auth::id()]);
      $faq = FAQ::create($data);
      $faq = new FAQResource($faq);

      return [
        'data' => $faq,
        'message' => __('messages.faq_success'),
        'success' => true
      ];
    } catch (\Exception $e) {
      return [
        'success' => false,
        'message' => $e->getMessage()
      ];
    }
  }

  public function updateFAQ($request, $id)
  {
    try {
      $validatedData = $request->validated();
      $user_id = Auth::id();
      $faq = FAQ::findOrFail($id);
      $faq->title = $validatedData['title'];
      $faq->description = $validatedData['description'];
      $faq->user_id     = $user_id;
      $faq->save();

      $faq = new FAQResource($faq);

      return [
        'data' => $faq,
        'message' => __('messages.faq_update_success'),
        'success' => true
      ];
    } catch (\Exception $e) {
      return [
        'success' => false,
        'message' => $e->getMessage()
      ];
    }    
  }

  public function viewFAQ($id)
  {
    try{
      $faq = FAQ::findOrFail($id);
      $faq = new FAQResource($faq);
      return [
        'data' => $faq,
        'message' => __('messages.faq_success_get'),
        'success' => true
      ];      
    }catch(\Exception $e){
      return [
        'success' => false,
        'message' => $e->getMessage()
      ];
    }
  }

  public function deleteFAQ($id)
  {
    try {
      $faq = FAQ::findOrFail($id);
      $faq->delete();
      return [
        'data' => [],
        'message' => __('messages.faq_delete_success'),
        'success' => true
      ];
    } catch (\Exception $e) {
      return [
        'success' => false,
        'message' => $e->getMessage()
      ];
    }    
  }
}