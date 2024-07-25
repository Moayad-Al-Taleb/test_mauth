<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * إنشاء مثيل جديد من AuthController.
     *
     * يحدد الـ middleware ليطبق على كل الطرق باستثناء 'login' و 'register'.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * الحصول على توكن JWT عبر بيانات الاعتماد المقدمة.
     *
     * يتحقق من صحة بيانات الاعتماد ويقوم بإنشاء توكن إذا كانت البيانات صحيحة.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // محاولة تسجيل الدخول والتحقق من صحة البيانات
        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    /**
     * تسجيل مستخدم جديد.
     *
     * يقوم بإنشاء مستخدم جديد وتشفير كلمة المرور.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // التحقق من صحة بيانات التسجيل
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        // إنشاء المستخدم وتشفير كلمة المرور
        $user = User::create(
            array_merge(
                $validator->validated(),
                ['password' => bcrypt($request->password)]
            )
        );

        return response()->json([
            'message' => 'تم تسجيل المستخدم بنجاح',
            'user' => $user
        ], 201);
    }

    /**
     * تسجيل خروج المستخدم (إبطال التوكن).
     *
     * يقوم بتسجيل خروج المستخدم وإبطال توكن الجلسة الحالية.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'تم تسجيل خروج المستخدم بنجاح']);
    }

    /**
     * تحديث التوكن.
     *
     * يقوم بتحديث توكن المستخدم وإرجاع التوكن الجديد.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * الحصول على معلومات المستخدم المصادق عليه.
     *
     * يعرض بيانات المستخدم المصادق عليه حاليًا.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(auth()->user());
    }

    /**
     * الحصول على هيكلية توكن الـ JWT.
     *
     * يعيد هيكلية التوكن بما في ذلك التوكن نفسه ونوعه ومدة صلاحيته ومعلومات المستخدم.
     *
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}
