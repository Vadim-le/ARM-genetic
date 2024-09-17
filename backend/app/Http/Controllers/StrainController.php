<?php

namespace App\Http\Controllers;

use App\Models\strain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreStrainRequest;
use App\Http\Requests\UpdateStrainRequest;
use Symfony\Component\HttpFoundation\Response;

class StrainController extends Controller
{
    public function store(StoreStrainRequest $request)
    {
        if (!Auth::user()->can('create', Strain::class)) {
            return $this->errorResponse('Отсутствуют разрешения', [], 403);
        }

        $strain = Strain::create($request->validated() + [
            'author_id' => Auth::id(),
        ]);

        return $this->successResponse($strain, 'Запись о штамме успешно создана', 200);
    }

    public function update(UpdateStrainRequest $request, int $id)
{
    Log::info('Update');
    
    // Проверяем, существует ли запись
    $strain = Strain::find($id);
    if (!$strain) {
        return $this->errorResponse('Запись не найдена', [], Response::HTTP_NOT_FOUND);
    }

    // Проверяем разрешения
    if (!Auth::user()->can('update', $strain)) {
        return $this->errorResponse('Отсутствуют разрешения', [], 403);
    }

    // Выполняем валидацию
    $validatedData = $request->validated();

    // Обновляем запись
    $strain->update($validatedData);
    
    return $this->successResponse(['strain' => $strain], 'Запись о штамме успешно обновлена', 200);
}

}
