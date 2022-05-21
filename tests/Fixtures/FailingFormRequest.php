<?php

namespace NiclasVanEyk\TransactionalControllers\Tests\Fixtures;

use Illuminate\Foundation\Http\FormRequest;

class FailingFormRequest extends FormRequest
{
    public function rules()
    {
        return ['i-do-not-exist' => ['required']];
    }
}
