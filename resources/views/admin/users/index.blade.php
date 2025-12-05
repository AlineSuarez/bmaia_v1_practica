@extends('layouts.admin')

@section('title', 'Gestión de Usuarios')

@section('content')
<div style="padding: 20px;">
    <!-- Header Unificado con Buscador -->
    <div style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.95) 100%);
                backdrop-filter: blur(10px);
                padding: 25px 30px;
                margin-bottom: 30px;
                border-radius: 15px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                border: 1px solid rgba(148, 163, 184, 0.1);">

        <!-- Título y Total -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <div>
                <h2 style="margin: 0; color: #0f172a; font-weight: 700; font-size: 1.75rem;">
                    <i class="fas fa-users" style="color: #2563eb; margin-right: 10px;"></i>
                    Gestión de Usuarios
                </h2>
                <p style="margin: 5px 0 0 0; color: #64748b; font-size: 0.95rem;">
                    Administra todos los usuarios del sistema
                </p>
            </div>
            <div style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
                        color: white;
                        padding: 12px 20px;
                        border-radius: 10px;
                        font-weight: 600;
                        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);">
                <i class="fas fa-user-friends" style="margin-right: 8px;"></i>
                Total: {{ $users->total() }} usuarios
            </div>
        </div>

        <!-- Alertas -->
        @if(session('success'))
        <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                    color: white;
                    padding: 15px 20px;
                    margin-bottom: 20px;
                    border-radius: 12px;
                    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
                    display: flex;
                    align-items: center;
                    gap: 12px;">
            <i class="fas fa-check-circle" style="font-size: 1.25rem;"></i>
            <span style="font-weight: 500;">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                    color: white;
                    padding: 15px 20px;
                    margin-bottom: 20px;
                    border-radius: 12px;
                    box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.3);
                    display: flex;
                    align-items: center;
                    gap: 12px;">
            <i class="fas fa-exclamation-circle" style="font-size: 1.25rem;"></i>
            <span style="font-weight: 500;">{{ session('error') }}</span>
        </div>
        @endif

        <!-- Buscador integrado -->
        <form method="GET" action="{{ route('admin.users.index') }}" style="margin: 0;">
            <div style="display: flex; gap: 12px; align-items: center;">
                <div style="flex: 1; position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1rem;"></i>
                    <input type="text"
                           id="searchInput"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Buscar por nombre, email, plan, estado, teléfono..."
                           style="width: 100%;
                                  padding: 12px 15px 12px 45px;
                                  border: 2px solid #e2e8f0;
                                  border-radius: 10px;
                                  font-size: 1rem;
                                  transition: all 0.2s;
                                  outline: none;"
                           onfocus="this.style.borderColor='#2563eb'; this.style.boxShadow='0 0 0 3px rgba(37, 99, 235, 0.1)'"
                           onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                </div>
                <button type="submit"
                        style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
                               color: white;
                               padding: 12px 24px;
                               border: none;
                               border-radius: 10px;
                               font-weight: 600;
                               cursor: pointer;
                               display: inline-flex;
                               align-items: center;
                               gap: 8px;
                               box-shadow: 0 2px 4px rgba(37, 99, 235, 0.3);
                               transition: all 0.2s;"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(37, 99, 235, 0.4)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(37, 99, 235, 0.3)'">
                    <i class="fas fa-search"></i>
                    Buscar
                </button>
                @if(request('search'))
                <a href="{{ route('admin.users.index') }}"
                   style="background: linear-gradient(135deg, #64748b 0%, #475569 100%);
                          color: white;
                          padding: 12px 24px;
                          border-radius: 10px;
                          text-decoration: none;
                          font-weight: 600;
                          display: inline-flex;
                          align-items: center;
                          gap: 8px;
                          box-shadow: 0 2px 4px rgba(100, 116, 139, 0.3);
                          transition: all 0.2s;"
                   onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(100, 116, 139, 0.4)'"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(100, 116, 139, 0.3)'">
                    <i class="fas fa-times"></i>
                    Limpiar
                </a>
                @endif
            </div>

            <!-- Filtros adicionales -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-top: 15px;">
                <!-- Filtro por Plan -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 6px;">
                        <i class="fas fa-crown" style="margin-right: 5px;"></i>Plan
                    </label>
                    <select name="filter_plan"
                            style="width: 100%;
                                   padding: 10px 12px;
                                   border: 2px solid #e2e8f0;
                                   border-radius: 8px;
                                   font-size: 0.9rem;
                                   background: white;
                                   cursor: pointer;
                                   transition: all 0.2s;"
                            onfocus="this.style.borderColor='#2563eb'"
                            onblur="this.style.borderColor='#e2e8f0'">
                        <option value="">Todos los planes</option>
                        <option value="afc" {{ request('filter_plan') == 'afc' ? 'selected' : '' }}>AFC</option>
                        <option value="me" {{ request('filter_plan') == 'me' ? 'selected' : '' }}>ME</option>
                        <option value="ge" {{ request('filter_plan') == 'ge' ? 'selected' : '' }}>GE</option>
                        <option value="drone" {{ request('filter_plan') == 'drone' ? 'selected' : '' }}>Drone</option>
                        <option value="sin_plan" {{ request('filter_plan') == 'sin_plan' ? 'selected' : '' }}>Sin Plan</option>
                    </select>
                </div>

                <!-- Filtro por Estado -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 6px;">
                        <i class="fas fa-signal" style="margin-right: 5px;"></i>Estado
                    </label>
                    <select name="filter_estado"
                            style="width: 100%;
                                   padding: 10px 12px;
                                   border: 2px solid #e2e8f0;
                                   border-radius: 8px;
                                   font-size: 0.9rem;
                                   background: white;
                                   cursor: pointer;
                                   transition: all 0.2s;"
                            onfocus="this.style.borderColor='#2563eb'"
                            onblur="this.style.borderColor='#e2e8f0'">
                        <option value="">Todos los estados</option>
                        <option value="activo" {{ request('filter_estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="proximo" {{ request('filter_estado') == 'proximo' ? 'selected' : '' }}>Próximo a Vencer</option>
                        <option value="vencido" {{ request('filter_estado') == 'vencido' ? 'selected' : '' }}>Plan Vencido</option>
                        <option value="sin_plan" {{ request('filter_estado') == 'sin_plan' ? 'selected' : '' }}>Sin Plan</option>
                    </select>
                </div>

                <!-- Filtro por Fecha Desde -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 6px;">
                        <i class="fas fa-calendar-alt" style="margin-right: 5px;"></i>Fecha Desde
                    </label>
                    <input type="date"
                           name="fecha_desde"
                           value="{{ request('fecha_desde') }}"
                           style="width: 100%;
                                  padding: 10px 12px;
                                  border: 2px solid #e2e8f0;
                                  border-radius: 8px;
                                  font-size: 0.9rem;
                                  transition: all 0.2s;"
                           onfocus="this.style.borderColor='#2563eb'"
                           onblur="this.style.borderColor='#e2e8f0'">
                </div>

                <!-- Filtro por Fecha Hasta -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 6px;">
                        <i class="fas fa-calendar-check" style="margin-right: 5px;"></i>Fecha Hasta
                    </label>
                    <input type="date"
                           name="fecha_hasta"
                           value="{{ request('fecha_hasta') }}"
                           style="width: 100%;
                                  padding: 10px 12px;
                                  border: 2px solid #e2e8f0;
                                  border-radius: 8px;
                                  font-size: 0.9rem;
                                  transition: all 0.2s;"
                           onfocus="this.style.borderColor='#2563eb'"
                           onblur="this.style.borderColor='#e2e8f0'">
                </div>
            </div>

            <!-- Botón Aplicar Filtros -->
            @if(request('filter_plan') || request('filter_estado') || request('fecha_desde') || request('fecha_hasta'))
            <div style="display: flex; gap: 12px; margin-top: 15px;">
                <button type="submit"
                        style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                               color: white;
                               padding: 10px 20px;
                               border: none;
                               border-radius: 8px;
                               font-weight: 600;
                               cursor: pointer;
                               display: inline-flex;
                               align-items: center;
                               gap: 8px;
                               font-size: 0.9rem;
                               box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
                               transition: all 0.2s;"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(16, 185, 129, 0.4)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(16, 185, 129, 0.3)'">
                    <i class="fas fa-filter"></i>
                    Aplicar Filtros
                </button>
                <a href="{{ route('admin.users.index') }}"
                   style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                          color: white;
                          padding: 10px 20px;
                          border-radius: 8px;
                          text-decoration: none;
                          font-weight: 600;
                          display: inline-flex;
                          align-items: center;
                          gap: 8px;
                          font-size: 0.9rem;
                          box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
                          transition: all 0.2s;"
                   onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(239, 68, 68, 0.4)'"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(239, 68, 68, 0.3)'">
                    <i class="fas fa-times-circle"></i>
                    Limpiar Filtros
                </a>
            </div>
            @endif
        </form>
        @if(request('search'))
        <div style="margin-top: 15px; color: #64748b; font-size: 0.9rem;">
            <i class="fas fa-info-circle" style="color: #3b82f6; margin-right: 5px;"></i>
            Mostrando resultados para: <strong style="color: #0f172a;">"{{ request('search') }}"</strong>
        </div>
        @endif
    </div>

    <!-- Tabla de usuarios -->
    <div style="background: white;
                border-radius: 15px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="table table-hover" style="margin: 0;">
                <thead style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-bottom: 2px solid #cbd5e1;">
                    <tr>
                        <th style="padding: 18px 20px; font-weight: 700; color: #0f172a; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <i class="fas fa-hashtag" style="color: #64748b; margin-right: 8px;"></i>ID
                        </th>
                        <th style="padding: 18px 20px; font-weight: 700; color: #0f172a; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <i class="fas fa-user" style="color: #64748b; margin-right: 8px;"></i>Nombre
                        </th>
                        <th style="padding: 18px 20px; font-weight: 700; color: #0f172a; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <i class="fas fa-envelope" style="color: #64748b; margin-right: 8px;"></i>Email
                        </th>
                        <th style="padding: 18px 20px; font-weight: 700; color: #0f172a; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <i class="fas fa-phone" style="color: #64748b; margin-right: 8px;"></i>Teléfono
                        </th>
                        <th style="padding: 18px 20px; font-weight: 700; color: #0f172a; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <i class="fas fa-warehouse" style="color: #64748b; margin-right: 8px;"></i>Apiarios
                        </th>
                        <th style="padding: 18px 20px; font-weight: 700; color: #0f172a; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <i class="fas fa-crown" style="color: #64748b; margin-right: 8px;"></i>Plan
                        </th>
                        <th style="padding: 18px 20px; font-weight: 700; color: #0f172a; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <i class="fas fa-file-invoice" style="color: #64748b; margin-right: 8px;"></i>Facturación
                        </th>
                        <th style="padding: 18px 20px; font-weight: 700; color: #0f172a; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <i class="fas fa-calendar" style="color: #64748b; margin-right: 8px;"></i>Registro
                        </th>
                        <th style="padding: 18px 20px; font-weight: 700; color: #0f172a; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <i class="fas fa-toggle-on" style="color: #64748b; margin-right: 8px;"></i>Estado
                        </th>
                        <th style="padding: 18px 20px; font-weight: 700; color: #0f172a; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; text-align: center;">
                            <i class="fas fa-cog" style="color: #64748b; margin-right: 8px;"></i>Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background-color 0.2s;"
                        onmouseover="this.style.backgroundColor='#f8fafc'"
                        onmouseout="this.style.backgroundColor='white'">
                        <td style="padding: 18px 20px; color: #64748b; font-weight: 600;">
                            #{{ $user->id }}
                        </td>
                        <td style="padding: 18px 20px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 40px;
                                            height: 40px;
                                            border-radius: 50%;
                                            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            color: white;
                                            font-weight: 700;
                                            font-size: 1rem;
                                            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.3);">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span style="font-weight: 600; color: #0f172a;">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td style="padding: 18px 20px; color: #64748b;">
                            <i class="fas fa-envelope" style="color: #94a3b8; margin-right: 6px;"></i>
                            {{ $user->email }}
                        </td>
                        <td style="padding: 18px 20px; color: #64748b;">
                            <i class="fas fa-phone" style="color: #94a3b8; margin-right: 6px;"></i>
                            @if($user->telefono && trim($user->telefono) !== '')
                                {{ $user->telefono }}
                            @else
                                <span style="color: #94a3b8; font-style: italic;">No especificado</span>
                            @endif
                        </td>
                        <td style="padding: 18px 20px;">
                            <span style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                                        color: white;
                                        padding: 6px 12px;
                                        border-radius: 8px;
                                        font-weight: 600;
                                        font-size: 0.875rem;
                                        display: inline-flex;
                                        align-items: center;
                                        gap: 6px;">
                                <i class="fas fa-warehouse"></i>
                                {{ $user->apiarios_count }}
                            </span>
                        </td>
                        <td style="padding: 18px 20px;">
                            @php
                                $planColors = [
                                    'afc' => 'linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)',
                                    'me' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                                    'ge' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                                    'drone' => 'linear-gradient(135deg, #64748b 0%, #475569 100%)',
                                ];
                                $planLabels = [
                                    'afc' => 'AFC',
                                    'me' => 'ME',
                                    'ge' => 'GE',
                                    'drone' => 'Drone',
                                ];

                                // Usar el plan real de la BD, sin fallback
                                $userPlan = $user->plan;
                                $hasPlan = $userPlan !== null && $userPlan !== '' && trim($userPlan) !== '';

                                if ($hasPlan) {
                                    $planColor = $planColors[$userPlan] ?? 'linear-gradient(135deg, #94a3b8 0%, #64748b 100%)';
                                    $planLabel = $planLabels[$userPlan] ?? strtoupper($userPlan);
                                }
                            @endphp
                            @if($hasPlan)
                                <span style="background: {{ $planColor }};
                                            color: white;
                                            padding: 6px 12px;
                                            border-radius: 8px;
                                            font-weight: 600;
                                            font-size: 0.875rem;
                                            display: inline-flex;
                                            align-items: center;
                                            gap: 6px;">
                                    <i class="fas fa-crown"></i>
                                    {{ $planLabel }}
                                </span>
                            @else
                                <span style="color: #000000; font-size: 0.875rem;">No especificado</span>
                            @endif
                        </td>
                        <td style="padding: 18px 20px;">
                            @php
                                $tieneFacturas = ($user->facturas && $user->facturas->count() > 0) || ($user->boletas && $user->boletas->count() > 0);
                            @endphp
                            <a href="{{ route('admin.users.billing', $user->id) }}"
                               title="Ver facturación de {{ $user->name }}"
                               style="text-decoration: none;">
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                                    @if($user->datosFacturacion)
                                        <span style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                                                    color: white;
                                                    padding: 6px 12px;
                                                    border-radius: 8px;
                                                    font-weight: 600;
                                                    font-size: 0.875rem;
                                                    display: inline-flex;
                                                    align-items: center;
                                                    gap: 6px;
                                                    cursor: pointer;
                                                    transition: all 0.2s;
                                                    flex: 1;"
                                                onmouseover="this.style.transform='scale(1.02)'"
                                                onmouseout="this.style.transform='scale(1)'">
                                            <i class="fas fa-file-invoice"></i>
                                            Factura
                                        </span>
                                    @else
                                        <span style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                                                    color: white;
                                                    padding: 6px 12px;
                                                    border-radius: 8px;
                                                    font-weight: 600;
                                                    font-size: 0.875rem;
                                                    display: inline-flex;
                                                    align-items: center;
                                                    gap: 6px;
                                                    cursor: pointer;
                                                    transition: all 0.2s;
                                                    flex: 1;"
                                                onmouseover="this.style.transform='scale(1.02)'"
                                                onmouseout="this.style.transform='scale(1)'">
                                            <i class="fas fa-receipt"></i>
                                            Boleta
                                        </span>
                                    @endif
                                    @if($tieneFacturas)
                                        <span style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                                                    color: white;
                                                    padding: 4px;
                                                    border-radius: 50%;
                                                    font-weight: 600;
                                                    font-size: 0.7rem;
                                                    display: inline-flex;
                                                    align-items: center;
                                                    justify-content: center;
                                                    width: 22px;
                                                    height: 22px;
                                                    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);">
                                            <i class="fas fa-check"></i>
                                        </span>
                                    @else
                                        <span style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                                                    color: white;
                                                    padding: 4px;
                                                    border-radius: 50%;
                                                    font-weight: 600;
                                                    font-size: 0.7rem;
                                                    display: inline-flex;
                                                    align-items: center;
                                                    justify-content: center;
                                                    width: 22px;
                                                    height: 22px;
                                                    box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);">
                                            <i class="fas fa-times"></i>
                                        </span>
                                    @endif
                                </div>
                            </a>
                        </td>
                        <td style="padding: 18px 20px; color: #64748b; font-size: 0.875rem;">
                            <i class="fas fa-calendar-alt" style="color: #94a3b8; margin-right: 6px;"></i>
                            {{ $user->created_at->format('d/m/Y') }}
                        </td>
                        <td style="padding: 18px 20px; text-align: center;">
                            @php
                                $estadoBadge = '';
                                $estadoTexto = 'Sin Plan';
                                $estadoIcono = 'fa-times-circle';

                                if ($user->plan && $user->fecha_vencimiento) {
                                    $ahora = \Carbon\Carbon::now();
                                    $vencimiento = \Carbon\Carbon::parse($user->fecha_vencimiento);
                                    $diasRestantes = $ahora->diffInDays($vencimiento, false);

                                    if ($diasRestantes < 0) {
                                        // Plan vencido
                                        $estadoBadge = 'background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);';
                                        $estadoTexto = 'Plan Vencido';
                                        $estadoIcono = 'fa-exclamation-circle';
                                    } elseif ($diasRestantes <= 7) {
                                        // Próximo a vencer (7 días o menos)
                                        $estadoBadge = 'background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);';
                                        $estadoTexto = 'Próximo a Vencer';
                                        $estadoIcono = 'fa-clock';
                                    } else {
                                        // Plan activo
                                        $estadoBadge = 'background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);';
                                        $estadoTexto = 'Activo';
                                        $estadoIcono = 'fa-check-circle';
                                    }
                                } else {
                                    // Sin plan
                                    $estadoBadge = 'background: linear-gradient(135deg, #64748b 0%, #475569 100%); box-shadow: 0 2px 4px rgba(100, 116, 139, 0.3);';
                                }
                            @endphp
                            <span style="{{ $estadoBadge }}
                                        color: white;
                                        padding: 6px 14px;
                                        border-radius: 20px;
                                        font-size: 0.8rem;
                                        font-weight: 600;
                                        display: inline-flex;
                                        align-items: center;
                                        gap: 6px;">
                                <i class="fas {{ $estadoIcono }}"></i>
                                {{ $estadoTexto }}
                            </span>
                        </td>
                        <td style="padding: 18px 20px; text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('admin.users.show', $user->id) }}"
                                   title="Ver perfil de {{ $user->name }}"
                                   style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                                          color: white;
                                          width: 36px;
                                          height: 36px;
                                          border-radius: 8px;
                                          text-decoration: none;
                                          display: inline-flex;
                                          align-items: center;
                                          justify-content: center;
                                          box-shadow: 0 2px 4px rgba(37, 99, 235, 0.3);
                                          transition: all 0.2s;"
                                   onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(37, 99, 235, 0.4)'"
                                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(37, 99, 235, 0.3)'">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.users.destroy', $user->id) }}"
                                      method="POST"
                                      class="delete-user-form"
                                      data-user-name="{{ $user->name }}"
                                      data-user-email="{{ $user->email }}"
                                      style="display: inline-block; margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="btn-delete-user"
                                            title="Eliminar a {{ $user->name }}"
                                            style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                                                   color: white;
                                                   width: 36px;
                                                   height: 36px;
                                                   border-radius: 8px;
                                                   border: none;
                                                   display: inline-flex;
                                                   align-items: center;
                                                   justify-content: center;
                                                   cursor: pointer;
                                                   box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
                                                   transition: all 0.2s;"
                                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(239, 68, 68, 0.4)'"
                                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(239, 68, 68, 0.3)'">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" style="padding: 40px; text-align: center; color: #64748b;">
                            <i class="fas fa-users" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px; display: block;"></i>
                            <p style="margin: 0; font-size: 1.125rem; font-weight: 600;">No hay usuarios registrados</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($users->hasPages())
        <div style="padding: 20px; border-top: 1px solid #f1f5f9;">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Obtener todos los botones de eliminar
        const deleteButtons = document.querySelectorAll('.btn-delete-user');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const form = this.closest('.delete-user-form');
                const userName = form.dataset.userName;
                const userEmail = form.dataset.userEmail;

                Swal.fire({
                    title: '¿Eliminar usuario?',
                    html: `
                        <div style="text-align: left; padding: 20px;">
                            <p style="margin: 0 0 15px 0; color: #64748b; font-size: 0.95rem;">
                                Estás a punto de eliminar al siguiente usuario:
                            </p>
                            <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
                                        padding: 15px;
                                        border-radius: 10px;
                                        border-left: 4px solid #ef4444;">
                                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                                    <div style="width: 40px;
                                                height: 40px;
                                                border-radius: 50%;
                                                background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                                color: white;
                                                font-weight: 700;">
                                        ${userName.charAt(0).toUpperCase()}
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: #0f172a; font-size: 1rem;">${userName}</div>
                                        <div style="color: #64748b; font-size: 0.875rem;">${userEmail}</div>
                                    </div>
                                </div>
                            </div>
                            <p style="margin: 15px 0 0 0; color: #ef4444; font-weight: 600; font-size: 0.9rem;">
                                <i class="fas fa-exclamation-triangle" style="margin-right: 6px;"></i>
                                Esta acción no se puede deshacer
                            </p>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fas fa-trash-alt"></i> Sí, eliminar',
                    cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                    reverseButtons: true,
                    customClass: {
                        popup: 'swal-custom-popup',
                        title: 'swal-custom-title',
                        confirmButton: 'swal-custom-confirm',
                        cancelButton: 'swal-custom-cancel'
                    },
                    width: '600px',
                    padding: '2rem',
                    backdrop: 'rgba(15, 23, 42, 0.7)',
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown animate__faster'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp animate__faster'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mostrar loading
                        Swal.fire({
                            title: 'Eliminando usuario...',
                            html: 'Por favor espera un momento',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Enviar el formulario
                        form.submit();
                    }
                });
            });
        });
    });
</script>

<style>
    .swal-custom-popup {
        border-radius: 15px !important;
        font-family: 'Outfit', sans-serif !important;
    }

    .swal-custom-title {
        color: #0f172a !important;
        font-weight: 700 !important;
        font-size: 1.5rem !important;
    }

    .swal-custom-confirm {
        border-radius: 8px !important;
        padding: 12px 24px !important;
        font-weight: 600 !important;
        font-size: 0.95rem !important;
        box-shadow: 0 4px 6px rgba(239, 68, 68, 0.3) !important;
        transition: all 0.2s !important;
    }

    .swal-custom-confirm:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 10px rgba(239, 68, 68, 0.4) !important;
    }

    .swal-custom-cancel {
        border-radius: 8px !important;
        padding: 12px 24px !important;
        font-weight: 600 !important;
        font-size: 0.95rem !important;
        box-shadow: 0 4px 6px rgba(100, 116, 139, 0.3) !important;
        transition: all 0.2s !important;
    }

    .swal-custom-cancel:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 10px rgba(100, 116, 139, 0.4) !important;
    }
</style>

<script>
    // Búsqueda reactiva con debounce
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const form = searchInput.closest('form');
        let debounceTimer;

        searchInput.addEventListener('input', function() {
            // Limpiar el temporizador anterior
            clearTimeout(debounceTimer);

            // Establecer un nuevo temporizador para enviar el formulario después de 500ms
            debounceTimer = setTimeout(function() {
                form.submit();
            }, 500);
        });
    });
</script>

@endpush
