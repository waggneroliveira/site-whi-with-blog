@extends('client.core.client')
@section('content')
    <style>

        .banner-inner{
            height: 335px;
        }
        #footer{
            display: none;
        }
        @media (max-width: 476px) {
            .call-to-action i {
                height: 40px;
                width: 40px;
                font-size: 1.125rem;
            }
            .call-to-action {
                height: 40px;
                font-size: 0.938rem;
                width: 230px;
                padding-left: 0 !important;
                gap: 8px !important;
            }
        }
    </style>
   <div class="banner-inner position-relative">
      <div class="max-width m-auto">
         <h1 class="h2 m-0 text-white text-uppercase rethink-sans-bold font-38 d-block" data-aos="fade-right" data-aos-delay="100">Página não encontrada</h1>
         <ol class="breadcrumb mt-4 align-items-center" data-aos="fade-right" data-aos-delay="500">
            <li class="breadcrumb-item">
               <a href="{{route('index')}}">
                  <i class="fas fa-home me-2"></i>Home
               </a>
            </li>
            <li class="breadcrumb-item blog">
               <a href="{{route('blog')}}">
                  <i class="fas fa-newspaper me-2"></i>Blog WHI
               </a>
            </li>		               			
         </ol>
      </div>
   </div>
    <div class="container-fluid d-flex align-items-center justify-content-center h-100 pb-4">
        <div class="text-center">
            <!-- Ícone ou imagem do erro -->
            <div class="mb-0">
                <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 5rem;"></i>
            </div>
            
            <!-- Título e mensagem -->
            <h1 class="display-1 fw-bold emphasis rethink-sans-bold">404</h1>
            <h2 class="mb-4 rethink-sans-bold" style="color: #10131C;">Página não encontrada</h2>
            <p class="lead mb-4 text-center" style="color: #10131C;">
                Desculpe, a página que você está procurando não existe ou foi movida.
            </p>
            
            <!-- Botão de ação -->
            <div class="d-grid gap-2 d-md-flex justify-content-center">
                <a href="{{route('index')}}" class="rethink-sans-regular text-white call-to-action d-flex justify-content-start gap-2 align-items-center">
                    <i class="bi bi-house-fill me-2 rounded-circle d-flex justify-content-center align-items-center"></i>Voltar para o Início
                </a>
            </div>
        </div>
    </div>
@endsection