<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminAttendanceUpdateRequest extends FormRequest
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
            'clock_in' => 'required|date_format:H:i',
            'clock_out' => 'required|date_format:H:i',
            'break_start.*' => 'nullable',
            'break_end.*' => 'nullable',
            'reason' => 'required',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->clock_in>= $this->clock_out) {
                $validator->errors()->add(
                    'clock_in',
                    '出勤時間もしくは退勤時間が不適切な値です'
                );
            }
            foreach($this->break_start ?? [] as $index => $start) {

                $end = $this->break_end[$index] ?? null;

                if (($start && !$end) || (!$start && $end)) {
                    $validator->errors()->add(
                        "break_start.$index",
                        '休憩時間が不適切な値です'
                    );
                }

                if (empty($start)) {
                    continue;
                }

                if ($start < $this->clock_in || $start > $this->clock_out) {
                $validator->errors()->add(
                    "break_start.$index",
                    '休憩時間が不適切な値です'
                );
                }

                if($end && $end > $this->clock_out) {
                    $validator->errors()->add(
                        "break_end.$index",
                        '休憩時間もしくは退勤時間が不適切な値です'
                    );
                }

                if ($end && $end <= $start) {
                    $validator->errors()->add(
                        "break_end.$index",
                        '休憩時間が不適切な値です'
                    );
                }
            }
        });
    }

    public function messages()
    {
        return[
            'reason.required' => '備考を記入してください',
        ];
    }
}
