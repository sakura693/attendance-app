<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException; // 追加

class CorrectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reason' => 'required'
        ];
    }

    //カスタムバリデーション
    protected function passedValidation(){
        $clockIn = $this->input('clock_in_time');
        $clockOut = $this->input('clock_out_time');
        $breakStarts = $this->input('break_start', []);
        $breakEnds = $this->input('break_end', []);

        //出勤時間>退勤時間の場合のバリデーション
        if (strtotime($clockIn) >= strtotime($clockOut)){
            $this->failWithMessage('出勤時間もしくは退勤時間が不適切な値です。');
        }

        //休憩時間の追加チェック
        foreach ($breakStarts as $index=> $breakStart){
            $breakEnd = $breakEnds[$index] ?? null;
            if (
                //休憩開始時間が勤務時間外
                ($breakStart && (strtotime($breakStart) < strtotime($clockIn) || strtotime($breakStart) > strtotime($clockOut))) ||
                
                //休憩終了時間が勤務時間外
                ($breakEnd && (strtotime($breakEnd) < strtotime($clockIn) || strtotime($breakEnd)> strtotime($clockOut))) ||

                // 休憩開始時間が終了時間より後
                ($breakStart && $breakEnd && strtotime($breakStart) >= strtotime($breakEnd))
                ){
                $this->failWithMessage('休憩時間が勤務時間外です。');
            }
        }
    }

    public function messages(){
        return [
            'reason.required' => '備考を記入してください。'
        ];
    }

    protected function failWithMessage(string $message){
        throw \Illuminate\Validation\ValidationException::withMessages([
            //validation_errorはキーでバリデーションを管理するための識別子。このキーを使って、ビューで特定のエラーメッセージを取得できる。
            'validation_error' => [$message],
        ]);
    }
}
