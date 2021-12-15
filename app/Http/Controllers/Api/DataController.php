<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Data;
use App\Models\Info;

class DataController extends Controller
{

    public function index(Request $request)
    {
        $params = $request->query();
        $data = Data::select(["label", "value", "unit", "happened_at"])
            ->where(function ($query) use ($params) {
                $date = isset($params["date"]) ? $params["date"] : date("Y-m-d");
                return $query->whereBetween('happened_at', [$date . " 00:00:00", $date . " 23:59:59"]);
            })
            ->where(function ($query) use ($params) {
                if (isset($params["labels"])) {
                    $labels = preg_split('/\s*,+\s*/', $params["labels"], -1, PREG_SPLIT_NO_EMPTY);
                    if (count($labels) > 1) {
                        return $query->whereIn('label', $labels);
                    }
                    return $query->whereIn('label', [$params["labels"]]);
                }
            })
            ->orderBy('happened_at', 'asc')
            ->get();
        $info = Info::select(["name", "info"])->orderBy("name", "asc")->get();

        $result = [
            "info" => $info,
            "items" => $data
        ];
        return Response()->json($result);
    }

    public function store(Request $request)
    {
        try {
            $file_path = $request->file('file')->getRealPath();
            $file_open = fopen($file_path, 'r');
            $file_content = fread($file_open, filesize($file_path));
            $file_rows = explode(PHP_EOL, $file_content);
            $columns = $this->getFormattedColumnsFromFile($file_rows);

            $rows = $this->getFormattedRowsFromFile($file_rows, $columns);
            if (!$rows || $rows["status"] == false) {
                return Response()->status(401)->json($rows);
            }

            collect($rows["data"])->each(function ($row) {
                Data::updateOrCreate($row, $row);
            });

            $info = $this->getFormattedDescriptionFromFile($file_rows, $rows["line_of_info"]);
            if (count($info["data"]) >= 1) {
                collect($info["data"])->each(function ($row) {
                    Info::updateOrCreate($row, $row);
                });
            }
            return Response()->json(["status" => true, "msg" => "Your file was imported!"]);
        } catch (\Exception $e) {
            dd($e);
            return Response()->status(500)->json(["status" => true, "msg" => $e]);
        }
    }

    private function getFormattedColumnsFromFile($rows)
    {
        $arr_columns = [];
        if ($rows && count($rows) > 0) {
            foreach ($rows as $row) {
                if (empty($row)) continue;
                $columns = preg_split("/[\s]+/", $row, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($columns as $column) {
                    $split_column = preg_split("/[\[\]]/", trim($column), -1, PREG_SPLIT_NO_EMPTY);
                    array_push($arr_columns, [
                        'label' => strtolower($split_column[0]),
                        'unit' => strtolower($split_column[1]),
                    ]);
                }
                break;
            }
        }
        return $arr_columns;
    }

    private function getFormattedRowsFromFile($file_rows, $header)
    {
        // ignore header file
        $line_of_content = 2;
        $rows = array_slice($file_rows, $line_of_content);
        $line_of_info = null;
        $format_rows = [];
        foreach ($rows as $line => $row) {
            if (empty($row)) continue;
            if ($row == ">") {
                $line_of_info = $line + $line_of_content + 1;
                break;
            };
            $format_row_values = preg_split("/[\s]+/", trim($row), -1, PREG_SPLIT_NO_EMPTY);
            // formating timestamp
            $format_row_values[1] = "{$format_row_values['0']} {$format_row_values['1']}";
            $values = array_slice($format_row_values, 1);

            // checking total fields
            if (count($values) != count($header)) {
                return [
                    "status" => false,
                    "line_of_info" => $line_of_info,
                    "msg" => "Missing values in line " . $line + 1
                ];
            }

            // formating array
            $happened_at = $values[0];
            foreach ($values as $index => $value) {
                if ($header[$index]['label'] == "time") continue;
                array_push($format_rows, [
                    'label' => $header[$index]['label'],
                    'value' => $value,
                    'unit' => $header[$index]['unit'],
                    'happened_at' => $happened_at
                ]);
            }
        }
        return [
            "status" => true,
            "line_of_info" => $line_of_info,
            "data" => $format_rows
        ];
    }

    private function getFormattedDescriptionFromFile($file_rows, $line_of_info)
    {
        // ignore header file
        $rows = array_slice($file_rows, $line_of_info);
        $format_rows = [];
        foreach ($rows as $row) {
            if (empty($row)) continue;
            $format_row_values = preg_split("/[\:]+/", trim($row), -1, PREG_SPLIT_NO_EMPTY);

            if (count($format_row_values) > 1) {
                array_push($format_rows, [
                    'name' => trim($format_row_values[0]),
                    'info' => trim($format_row_values[1])
                ]);
            } else {
                array_push($format_rows, [
                    'name' => null,
                    'info' => trim($format_row_values[0])
                ]);
            }
        }
        return [
            "status" => true,
            "data" => $format_rows
        ];
    }
}