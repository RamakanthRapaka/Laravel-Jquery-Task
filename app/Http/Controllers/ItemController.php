<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Response;
use \Illuminate\Http\Response as Res;
use Illuminate\Database\QueryException as QueryException;
use Illuminate\Support\Facades\Log;
use App\Item;

class ItemController extends ApiController {

    public function __construct() {
        
    }

    public function SaveOrUpdate(Request $request) {

        $rules = array(
            'item_name' => 'required_without:id|regex:/^([a-zA-Z]+)(\s[a-zA-Z]+)*$/|max:50|unique:items,item_name,' . $request->input('id'),
            'id' => 'sometimes|nullable|numeric',
            'category' => 'sometimes|nullable|numeric',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
        }

        try {
            $item = NULL;
            $status_code = Res::HTTP_OK;
            $message = 'Item Updated!';
            if ($request->input('id') != NULL) {
                $item = Item::when($request->input('id'), function($query) use ($request) {
                            return $query->where('id', $request->input('id'));
                        })->first();
                if ($item === NULL) {
                    return $this->respond([
                                'status' => 'error',
                                'status_code' => Res::HTTP_NOT_FOUND,
                                'message' => 'Item Not Found!',
                    ]);
                }
                $item->category = $request->input('category');
                $item->save();
            }

            if ($item === NULL) {
                $status_code = Res::HTTP_CREATED;
                $message = 'Item Created!';
                $item = new Item;
                $item->item_name = $request->input('item_name');
                $item->save();
            }

            $items_list = $item->get()->toArray();

            return $this->respond([
                        'status' => 'success',
                        'status_code' => $status_code,
                        'data' => $items_list,
                        'message' => $message,
            ]);
        } catch (QueryException $e) {
            Log::emergency($e);
            return $this->respondInternalErrors();
        } catch (\PDOException $e) {
            Log::emergency($e);
            return $this->respondInternalErrors();
        } catch (\Exception $e) {
            Log::emergency($e);
            return $this->respondInternalErrors();
        }
    }

    public function GetItems(Request $request) {
        $rules = array(
            'item_name' => 'sometimes|nullable|regex:/^([a-zA-Z]+)(\s[a-zA-Z]+)*$/|max:50',
            'id' => 'sometimes|nullable|numeric',
            'category' => 'sometimes|nullable|numeric',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
        }

        try {
            $item = Item::when($request->input('id'), function($query) use ($request) {
                                return $query->where('id', $request->input('id'));
                            })
                            ->when($request->input('item_name'), function($query) use ($request) {
                                return $query->where('item_name', 'like', '%' . $request->input('item_name') . '%');
                            })
                            ->when($request->input('category'), function($query) use ($request) {
                                return $query->where('category', $request->input('category'));
                            })
                            ->get()->toArray();

            return $this->respond([
                        'status' => 'success',
                        'status_code' => Res::HTTP_OK,
                        'data' => $item,
                        'message' => 'Items List!',
            ]);
        } catch (QueryException $e) {
            Log::emergency($e);
            return $this->respondInternalErrors();
        } catch (\PDOException $e) {
            Log::emergency($e);
            return $this->respondInternalErrors();
        } catch (\Exception $e) {
            Log::emergency($e);
            return $this->respondInternalErrors();
        }
    }

}
