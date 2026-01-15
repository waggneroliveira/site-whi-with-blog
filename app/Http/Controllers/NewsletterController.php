<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use App\Repositories\SettingThemeRepository;
use App\Repositories\UserPermissionRepository;

class NewsletterController extends Controller
{

    public function index(UserPermissionRepository $userPermissionRepository)
    {
        $settingTheme = (new SettingThemeRepository())->settingTheme();
        
        if(!Auth::user()->hasRole('Super') && 
          !Auth::user()->can('usuario.tornar usuario master') &&
          !Auth::user()->hasPermissionTo('newsletter.visualizar')){
            return view('admin.error.403', compact('settingTheme'));
        }

        $newsletters = Newsletter::paginate(50);

        return view('admin.blades.newsletter.index', compact('newsletters'));
    }
 
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'email' => 'required|email',
    //         'term_privacy' => 'accepted',
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         Newsletter::create([
    //             'email' => $validated['email'],
    //             'term_privacy' => 1,
    //         ]);

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Cadastro realizado com sucesso!',
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return response()->json([
    //             'error' => true,
    //             'message' => 'Ocorreu um erro ao cadastrar. Por favor, tente novamente.',
    //             'details' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function store(Request $request)
    {
        // Headers CORS para permitir requisições cross-domain
        $allowedOrigin = 'https://seusiteestatico.com'; // Substitua pelo domínio do seu site estático
        $origin = $request->header('Origin');
        
        // Se o origin estiver na lista de permitidos ou for localhost para testes
        if (in_array($origin, [$allowedOrigin, 'http://localhost:8000', 'http://127.0.0.1:8000'])) {
            header("Access-Control-Allow-Origin: $origin");
        }
        
        header('Access-Control-Allow-Methods: POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400'); // 24 horas
        
        // Se for uma requisição OPTIONS (preflight), retornar sucesso
        if ($request->isMethod('OPTIONS')) {
            return response()->json([], 200);
        }
        
        // Validação
        $validated = $request->validate([
            'email' => 'required|email',
            'term_privacy' => 'accepted',
        ]);
        
        // Verificar se o email já existe
        $existing = Newsletter::where('email', $validated['email'])->first();
        if ($existing) {
            return response()->json([
                'error' => true,
                'message' => 'Este email já está cadastrado em nossa newsletter.',
            ], 422);
        }
        
        try {
            DB::beginTransaction();

            Newsletter::create([
                'email' => $validated['email'],
                'term_privacy' => 1,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cadastro realizado com sucesso!',
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => true,
                'message' => 'Ocorreu um erro ao cadastrar. Por favor, tente novamente.',
                'details' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function destroy(Newsletter $newsletter)
    {
        $newsletter->delete();
        Session::flash('success',__('dashboard.response_item_delete'));
        return redirect()->back();
    }

    public function destroySelected(Request $request)
    {    
        foreach ($request->deleteAll as $newsletterId) {
            $newsletter = Newsletter::find($newsletterId);
    
            if ($newsletter) {
                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($newsletter)
                    ->event('multiple_deleted')
                    ->withProperties([
                        'attributes' => [
                            'id' => $newsletterId,
                            'email' => $newsletter->email,
                            'term_privacy' => $newsletter->term_privacy,
                            'event' => 'multiple_deleted',
                        ]
                    ])
                    ->log('multiple_deleted');
            } else {
                \Log::warning("Item com ID $newsletterId não encontrado.");
            }
        }
    
        $deleted = Newsletter::whereIn('id', $request->deleteAll)->delete();
    
        if ($deleted) {
            return Response::json(['status' => 'success', 'message' => $deleted . ' '.__('dashboard.response_item_delete')]);
        }
    
        return Response::json(['status' => 'error', 'message' => 'Nenhum item foi deletado.'], 500);
    }
}
