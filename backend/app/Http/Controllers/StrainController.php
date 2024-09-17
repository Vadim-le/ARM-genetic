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
use App\Traits\PaginationTrait;
use App\Traits\QueryBuilderTrait;
use Symfony\Component\HttpFoundation\Response;

class StrainController extends Controller
{
    use QueryBuilderTrait, PaginationTrait;
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
        
        $strain = Strain::find($id);
        if (!$strain) {
            return $this->errorResponse('Запись не найдена', [], Response::HTTP_NOT_FOUND);
        }

        if (!Auth::user()->can('update', $strain)) {
            return $this->errorResponse('Отсутствуют разрешения', [], 403);
        }

        $validatedData = $request->validated();

        $strain->update($validatedData);
        
        return $this->successResponse(['strain' => $strain], 'Запись о штамме успешно обновлена', 200);
    }

    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        $strain = Strain::find($id);

        if (!$strain) {
            return $this->errorResponse('Запись не найдена', [], Response::HTTP_NOT_FOUND);
        }

        if (!Auth::user()->can('delete', $strain)) {
            return $this->errorResponse('Отсутствуют разрешения', [], 403);
        }

        $strain->delete();

        return $this->successResponse(['strain' => $strain], 'Запись о штамме успешно удалена', 200); 
        }
    public function getStrains(Request $request)
    {
        // Проверка прав доступа
        //if (!Auth::user()->can('viewAny', Strain::class)) {
        //    return $this->errorResponse('Нет прав на просмотр', [], Response::HTTP_FORBIDDEN);
        //}
    
        // Определяем необходимые поля
        $requiredFields = [
            "strain" => [
                "id",
                "name",
                "link",
                "place_of_allocation",
                "year_of_allocation",
                "type_of_bacteria",
                "created_at",
                "updated_at",
                "author_id",
            ],
        ];
    
        // Начинаем запрос
        $query = Strain::query();
    
        // Применяем фильтры, если они заданы
        if ($request->has('type_of_bacteria')) {
            $query->where('type_of_bacteria', $request->input('type_of_bacteria'));
        }
        if ($request->has('id')) {
            $query->where('id', $request->input('id'));
        }
        if ($request->has('name')) {
            $query->where('name', 'LIKE', '%' . $request->input('name') . '%');
        }
        if ($request->has('link')) {
            $query->where('link', 'LIKE', '%' . $request->input('link') . '%');
        }
        if ($request->has('place_of_allocation')) {
            $query->where('place_of_allocation', 'LIKE', '%' . $request->input('place_of_allocation') . '%');
        }
        if ($request->has('year_of_allocation')) {
            $query->where('year_of_allocation', $request->input('year_of_allocation'));
        }
    
        // Получаем результаты с пагинацией
        $perPage = $request->get('per_page', 10); // Значение по умолчанию 10
        $strains = $query->paginate($perPage);
    
        // Проверяем, есть ли записи
        if ($strains->isEmpty()) {
            return $this->errorResponse('Записи не найдены', [], Response::HTTP_NOT_FOUND);
        }
    
        // Формируем данные пагинации
        $paginationData = $this->makePaginationData($strains);
    
        // Возвращаем успешный ответ
        return $this->successResponse($strains->items(), $paginationData, Response::HTTP_OK);
    }
        

}
