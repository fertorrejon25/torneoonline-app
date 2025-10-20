@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">

        {{-- Mostrar nombre de temporada --}}
        @if(isset($temporada))
            <h2 class="text-2xl font-semibold mb-4 text-gray-700">
                Temporada: {{ $temporada->NombreTemporada }}
            </h2>
        @else
            <h2 class="text-xl text-gray-500 mb-4">Sin temporada activa</h2>
        @endif

        {{-- Botón para volver o cargar ranking --}}
        <div class="mb-4 flex justify-between items-center">
            <a href="{{ route('resultados.index') }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Ver Ranking Actualizado
            </a>

            @if(isset($temporada))
                <a href="{{ route('admin.dashboard', ['section' => 'ranking']) }}"
                   class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors">
                    Volver al Panel
                </a>
            @endif
        </div>

        {{-- Tabla de posiciones --}}
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border border-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">Pos</th>
                        <th class="px-4 py-3 text-left">Equipo</th>
                        <th class="px-4 py-3 text-center">PJ</th>
                        <th class="px-4 py-3 text-center">PG</th>
                        <th class="px-4 py-3 text-center">PE</th>
                        <th class="px-4 py-3 text-center">PP</th>
                        <th class="px-4 py-3 text-center">GF</th>
                        <th class="px-4 py-3 text-center">GC</th>
                        <th class="px-4 py-3 text-center">DIF</th>
                        <th class="px-4 py-3 text-center font-bold">PTS</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @isset($resultados)
                        @forelse($resultados as $index => $resultado)
                            <tr class="hover:bg-gray-50 transition-colors
                                @if($index === 0) bg-green-50 @endif
                                @if($index === count($resultados) - 1) bg-red-50 @endif
                            ">
                                <td class="px-4 py-3 font-bold">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-3">
                                        @if($resultado->equipo && $resultado->equipo->FotoEquipo)
                                            <img src="{{ asset('storage/' . $resultado->equipo->FotoEquipo) }}"
                                                 alt="{{ $resultado->equipo->NombreEquipos }}"
                                                 class="w-8 h-8 rounded-full object-cover">
                                        @endif
                                        <span class="font-medium">
                                            {{ $resultado->equipo->NombreEquipos ?? 'Equipo desconocido' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">{{ $resultado->partidos_jugados }}</td>
                                <td class="px-4 py-3 text-center text-green-600 font-semibold">{{ $resultado->partidos_ganados }}</td>
                                <td class="px-4 py-3 text-center text-yellow-600">{{ $resultado->partidos_empatados }}</td>
                                <td class="px-4 py-3 text-center text-red-600">{{ $resultado->partidos_perdidos }}</td>
                                <td class="px-4 py-3 text-center">{{ $resultado->goles_favor }}</td>
                                <td class="px-4 py-3 text-center">{{ $resultado->goles_contra }}</td>
                                <td class="px-4 py-3 text-center 
                                    @if($resultado->diferencia_goles > 0) text-green-600 font-semibold 
                                    @elseif($resultado->diferencia_goles < 0) text-red-600 font-semibold @endif">
                                    {{ $resultado->diferencia_goles > 0 ? '+' : '' }}{{ $resultado->diferencia_goles }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-xl font-bold text-blue-600">{{ $resultado->puntos }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                                    No hay resultados para esta temporada.
                                </td>
                            </tr>
                        @endforelse
                    @else
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                                No se han cargado resultados aún.
                            </td>
                        </tr>
                    @endisset
                </tbody>
            </table>
        </div>

        {{-- Leyenda --}}
        <div class="mt-6 p-4 bg-gray-50 rounded-lg text-sm text-gray-600">
            <p class="font-semibold mb-2">Leyenda:</p>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                <span><strong>PJ:</strong> Partidos Jugados</span>
                <span><strong>PG:</strong> Partidos Ganados</span>
                <span><strong>PE:</strong> Partidos Empatados</span>
                <span><strong>PP:</strong> Partidos Perdidos</span>
                <span><strong>GF:</strong> Goles a Favor</span>
                <span><strong>GC:</strong> Goles en Contra</span>
                <span><strong>DIF:</strong> Diferencia de Goles</span>
                <span><strong>PTS:</strong> Puntos</span>
            </div>
        </div>
    </div>
</div>
@endsection
