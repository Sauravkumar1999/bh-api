<?php

namespace App\Services;

use App\Http\Resources\MonthlyNewsResource;
use App\Models\MonthlyNews;
use Exception;

class MonthlyNewsService
{
    private $model;
    public $error_message;

    public function __construct(MonthlyNews $news)
    {
        $this->model = $news;
    }

    public function createOrUpdate($request, $id=null)
    {
        if(empty($id)){
            try {
                $news = $this->model->create($request->validated());
                return $news;
            } catch(Exception $e){
                $this->error_message = $e->getMessage();
                return false;
            }
        } else {
            try {
                if(empty($news = $this->model->find($id))){
                    $this->error_message = __('messages.no_news_fetched');
                    return false;
                }
                $news->update($request->validated());
                return $news;
            } catch(Exception $e){
                $this->error_message = $e->getMessage();
                return false;
            }
        }
    }

    public function destroy($id){
        if(empty($news = $this->model->find($id))){
            $this->error_message = __('messages.no_news_fetched');
            return false;
        }
        $news->delete();
        return true;
    }


    public function getSingle($id)
    {
        try {
            $news = $this->model->find($id);
            if (!empty($news)) {
                return [
                    'news' => MonthlyNewsResource::make($news),
                    'success' => true,
                ];
            }
            return [
                'success' => false,
                'message' => __('messages.news_not_found')
            ];
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'message' => $th->getMessage()
            ];
        }
    }
}
