<?php

namespace App\Services;

use App\Models\Sale;
use Modules\Sale\Events\SaleCreated;

class SaleService
{
    private $model;

    public function __construct(Sale $Sale)
    {
        $this->model = $Sale;
    }

    public function createSale($saleData)
    {
        try {
            $saleData->validated();
            $sale = new Sale();
            $sale->code = $this->getNextSaleCode();
            $sale->product_sale_day = $saleData['product_sale_day'];
            $sale->product_id = $saleData['product_id'];
            $sale->remark = $saleData['remark'];
            $sale->user_id = $saleData['user_id'];
            $sale->seller_id = $saleData['seller_id'];
            $sale->sales_price = str_replace(',', '', $saleData['sales_price']);
            // $sale->sales_type = $saleData['sales_type']; // since we remove the radio buttons
            $sale->product_price = $saleData['product_price'];
            $sale->fee_type = $saleData['fee_type'];
            $sale->take = str_replace(',', '', $saleData['take']);
            $sale->number_of_sales = str_replace(',', '', $saleData['number_of_sales']);
            $sale->sales_information = $saleData['sales_information'];
            // $sale->operating_income = str_replace(',', '', $saleData['operating_income']);
            $sale->sales_status = config('sale.default-sales-status');
            $res = $sale->save();
            //event(new SaleCreated($sale));
            return [
                'success' => true,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function updateSale($request, $id)
    {
        try {
            $this->model->findOrFail($id)->update($request->validated());

            return [
                'success' => true,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function deleteSale($id)
    {
        try {
            $this->model->findOrFail($id)->delete();

            return [
                'success' => true,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }


    /**
     * @return string
     */
    private function getNextSaleCode()
    {
        $latestSale = $this->model->orderBy('code', 'desc')->first();
        if ($latestSale) {
            $latestCode = $latestSale->code;
            $numericPart = (int) substr($latestCode, 1);
            $newNumericPart = $numericPart + 1;
            $newCode = 'S' . str_pad($newNumericPart, 5, '0', STR_PAD_LEFT);
        } else {
            $newCode = 'S00001';
        }
        return $newCode;
    }

}
