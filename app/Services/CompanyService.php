<?php

namespace App\Services;

use Exception;
use App\Models\Company;
use App\Traits\ManageCompanySquence;
use App\Events\CompanyCreated;
use App\Http\Resources\CompanyResource;

class CompanyService
{
  use ManageCompanySquence;

  private $validateRequestData;
  private $request;
  private $company;

  public function registerCompany($request)
  {
    $this->validateRequestData = $request->validated();
    $this->request =  $request;

    try {
      $this->createCompany();
      $company = new CompanyResource($this->company);
      return [
        'data' => $company,
        'message' => 'messages.company_success',
        'success' => true
      ];
    } catch (Exception $e) {
      return [
        'success' => false,
        'message' => $e->getMessage()
      ];
    }
  }

  public function createCompany()
  {
    $this->company = Company::create([
      'code' => $this->getCompanyNextCode(),
      'name' => $this->validateRequestData['name'],
      'url'  => $this->request['url'],
      'business_name' => $this->request['business_name'],
      'representative_name' => $this->request['representative_name'],
      'registration_number' => $this->request['registration_number'],
      'address'             => $this->request['address'],
      'scope_of_disclosure' => $this->request['scope_of_disclosure'],
      'registration_date'   => $this->request['registration_date']
    ]);

    event(new CompanyCreated($this->company));
  }

  public function updateCompany($request, $code)
  {
    $this->validateRequestData = $request->validated();

    try {
      $company = $this->findCompanyByCode($code);

      if (!is_null($company)) {
        $company->name = $this->validateRequestData['name'];
        $company->url  = $this->validateRequestData['url'];
        $company->business_name = $request['business_name'];
        $company->representative_name = $request['representative_name'];
        $company->registration_number = $request['registration_number'];
        $company->address = $request['address'];
        $company->scope_of_disclosure = $request['scope_of_disclosure'];
        $company->registration_date   = $request['registration_date'];
        $company->code = $code;
        $company->save();

        $company = new CompanyResource($company);
        return [
          'success' => true,
          'message' => "messages.company_update_success",
          'data' => $company
        ];
      } else {
        return [
          'success' => false,
          'message' => "messages.company_code_invalid"
        ];
      }
    } catch (Exception $e) {
      return [
        'success' => false,
        'message' => $e->getMessage()
      ];
    }
  }

  public function viewCompany($id)
  {
    try {
      $company = Company::findOrFail($id);
      $company = new CompanyResource($company);

      return [
        'success' => true,
        'message' => "messages.company_success",
        'data'    => $company
      ];
    } catch (Exception $e) {
      return [
        'success' => false,
        'message' => $e->getMessage()
      ];
    }
  }

  public function findCompanyByCode($code)
  {
    $company = Company::where('code', $code)->first();
    return $company;
  }

  public function deleteCompany($code)
  {
    try {
      $company = $this->findCompanyByCode($code);
      if (!is_null($company)) {
        $delete = $company->delete();
        if ($delete) {
          return [
            'success' => true,
            'message' => "messages.company_deleted"
          ];
        }
      } else {
        return [
          'success' => false,
          'message' => "messages.company_not_found"
        ];
      }
    } catch (Exception $e) {
      return [
        'success' => false,
        'message' => $e->getMessage()
      ];
    }
  }
}
