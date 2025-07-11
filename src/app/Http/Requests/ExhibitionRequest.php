<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'description' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image', 'mimes:jpeg,png'],
            'categories' => ['required', 'array'],
            'categories.*' => ['exists:categories,id'],
            'condition_id' => ['required', 'exists:conditions,id'],
            'brand' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '商品名は必須です',
            'description.required' => '商品説明は必須です',
            'description.max' => '商品説明は255文字以内で入力してください',
            'image.required' => '商品画像は必須です',
            'image.mimes' => '画像はjpegまたはpng形式でアップロードしてください',
            'categories.required' => '商品のカテゴリーを選択してください',
            'categories.*.exists' => '選択されたカテゴリーが無効です',
            'condition_id.required' => '商品の状態を選択してください',
            'condition_id.exists' => '選択された商品の状態が無効です',
            'price.required' => '価格は必須です',
            'price.numeric' => '価格は数値で入力してください',
            'price.min' => '価格は0円以上で入力してください',
        ];
    }
}
