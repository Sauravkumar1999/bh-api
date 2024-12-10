<?php 
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class FAQRequest extends FormRequest
{
  public function rules()
  {
    switch($this->method())
    {
      case 'POST':
        return $this->createRules();
      case 'PUT':
        return $this->updateRules();
      case 'PATCH':
        return $this->updateRules();
      case 'DELETE':
        return $this->deleteRules();
      default:
        return [];
    }
  }

  private function createRules()
  {
    return [
      'title' => 'required|string',
      'description' => 'required|string',
      'status' => 'required|integer'
    ];
  }

  private function updateRules()
  {
    return [
      'title' => 'sometimes|string',
      'description' => 'required|string',
      'status' => 'required|integer'
    ];
  }
  
  private function deleteRules()
  {
    return [];
  }
  
  public function messages()
  {
    return [
      'title.required'              => __('validation.required',['attribute' => __('validation.attributes.title')]),
      'description.required'        => __('validation.required',['attribute' => __('validation.attributes.description')]),
      'user_id.exists'              => __('validation.required',['attribute' => __('validation.attributes.user_id')])
    ];
  }
}

