<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehiculoController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->string('buscar')->trim()->toString();

        $vehiculos = Vehiculo::query()
            ->with('cliente')
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                $query->where(function ($query) use ($busqueda) {
                    $query->where('modelo', 'like', "%{$busqueda}%")
                        ->orWhere('anio', 'like', "%{$busqueda}%")
                        ->orWhere('placas', 'like', "%{$busqueda}%")
                        ->orWhereHas('cliente', function ($query) use ($busqueda) {
                            $query->where('nombre', 'like', "%{$busqueda}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $clientes = Cliente::orderBy('nombre')->get();

        return view('vehiculos.index', compact('vehiculos', 'clientes', 'busqueda'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $catalogoVehiculos = $this->catalogoVehiculosMexico();

        return view('vehiculos.create', compact('clientes', 'catalogoVehiculos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'tipo' => ['required', Rule::in(['moto', 'auto', 'servicio_pesado', 'camioneta', 'otro'])],
            'marca' => ['required', 'string', 'max:100'],
            'modelo' => ['required', 'string', 'max:100'],
            'anio' => ['required', 'integer', 'min:1980', 'max:2025'],
            'placas' => ['required', 'string', 'max:20', 'unique:vehiculos,placas'],
            'kilometraje_actual' => ['required', 'integer', 'min:0'],
            'tipo_combustible' => ['required', Rule::in(['gasolina', 'diesel', 'hibrido', 'electrico', 'gas'])],
            'estado' => ['required', Rule::in(['activo', 'inactivo', 'en_servicio'])],
            'observaciones' => ['nullable', 'string'],
        ]);

        Vehiculo::create($validated);

        return redirect()
            ->route('vehiculos.index')
            ->with('success', 'Vehículo registrado correctamente.');
    }

    public function update(Request $request, Vehiculo $vehiculo)
    {
        $validated = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'tipo' => ['required', Rule::in(['moto', 'auto', 'servicio_pesado', 'camioneta', 'otro'])],
            'marca' => ['required', 'string', 'max:100'],
            'modelo' => ['required', 'string', 'max:100'],
            'anio' => ['required', 'integer', 'min:1980', 'max:2025'],
            'placas' => ['required', 'string', 'max:20', Rule::unique('vehiculos', 'placas')->ignore($vehiculo)],
            'kilometraje_actual' => ['required', 'integer', 'min:0'],
            'tipo_combustible' => ['required', Rule::in(['gasolina', 'diesel', 'hibrido', 'electrico', 'gas'])],
            'estado' => ['required', Rule::in(['activo', 'inactivo', 'en_servicio'])],
            'observaciones' => ['nullable', 'string'],
        ]);

        $vehiculo->update($validated);

        return redirect()
            ->route('vehiculos.index')
            ->with('success', 'Vehículo actualizado correctamente.');
    }

    public function destroy(Vehiculo $vehiculo)
    {
        $vehiculo->update(['estado' => 'inactivo']);

        return redirect()
            ->route('vehiculos.index')
            ->with('success', 'Vehículo marcado como inactivo.');
    }

    private function catalogoVehiculosMexico(): array
    {
        return [
            'Acura' => ['Integra', 'Legend', 'MDX', 'RDX', 'TL', 'TLX'],
            'Audi' => ['80', 'A1', 'A3', 'A4', 'A5', 'A6', 'Q2', 'Q3', 'Q5', 'Q7', 'TT'],
            'BMW' => ['Serie 1', 'Serie 3', 'Serie 5', 'Serie 7', 'X1', 'X3', 'X5', 'X6'],
            'Buick' => ['Century', 'Enclave', 'Encore', 'LeSabre', 'Regal'],
            'Cadillac' => ['ATS', 'CTS', 'DeVille', 'Escalade', 'SRX', 'XT4', 'XT5'],
            'Chevrolet' => ['Astra', 'Aveo', 'Beat', 'Blazer', 'Camaro', 'Captiva', 'Cavalier', 'Chevy', 'Colorado', 'Corsa', 'Cruze', 'Equinox', 'Malibu', 'Onix', 'S-10', 'Silverado', 'Sonic', 'Spark', 'Suburban', 'Tahoe', 'Tracker', 'Trailblazer'],
            'Chrysler' => ['300', 'Cirrus', 'Grand Voyager', 'LeBaron', 'PT Cruiser', 'Shadow', 'Town & Country'],
            'Dodge' => ['Attitude', 'Avenger', 'Caravan', 'Challenger', 'Charger', 'Dakota', 'Dart', 'Durango', 'H100', 'Journey', 'Neon', 'RAM', 'Stratus'],
            'Fiat' => ['500', 'Argo', 'Ducato', 'Mobi', 'Palio', 'Panda', 'Pulse', 'Uno'],
            'Ford' => ['Bronco', 'Contour', 'Courier', 'EcoSport', 'Edge', 'Escape', 'Escort', 'Expedition', 'Explorer', 'F-150', 'Fiesta', 'Focus', 'Fusion', 'Lobo', 'Maverick', 'Mustang', 'Ranger', 'Topaz', 'Transit'],
            'GMC' => ['Acadia', 'Canyon', 'Jimmy', 'Sierra', 'Terrain', 'Yukon'],
            'Honda' => ['Accord', 'BR-V', 'City', 'Civic', 'CR-V', 'CR-Z', 'Element', 'Fit', 'HR-V', 'Odyssey', 'Pilot'],
            'Hyundai' => ['Accent', 'Atos', 'Creta', 'Elantra', 'Grand i10', 'H100', 'Santa Fe', 'Sonata', 'Tucson'],
            'Infiniti' => ['EX', 'FX', 'G', 'Q50', 'Q60', 'QX50', 'QX60', 'QX80'],
            'Isuzu' => ['ELF', 'Rodeo', 'Trooper'],
            'Jeep' => ['Cherokee', 'Compass', 'Gladiator', 'Grand Cherokee', 'Liberty', 'Patriot', 'Renegade', 'Wrangler'],
            'Kia' => ['Forte', 'K3', 'Niro', 'Rio', 'Seltos', 'Sorento', 'Soul', 'Sportage'],
            'Lincoln' => ['Aviator', 'Continental', 'Corsair', 'MKC', 'MKX', 'Navigator', 'Town Car'],
            'Mazda' => ['2', '3', '5', '6', 'B-Series', 'CX-3', 'CX-30', 'CX-5', 'CX-7', 'CX-9', 'MX-5'],
            'Mercedes-Benz' => ['Clase A', 'Clase B', 'Clase C', 'Clase E', 'Clase G', 'Clase M', 'Clase S', 'GLA', 'GLB', 'GLC', 'GLE', 'Sprinter'],
            'Mercury' => ['Cougar', 'Grand Marquis', 'Mystique', 'Sable', 'Villager'],
            'MG' => ['GT', 'HS', 'MG3', 'RX5', 'ZS'],
            'Mini' => ['Clubman', 'Cooper', 'Countryman', 'Paceman'],
            'Mitsubishi' => ['Eclipse', 'Endeavor', 'L200', 'Lancer', 'Mirage', 'Montero', 'Outlander', 'Xpander'],
            'Nissan' => ['Altima', 'Aprio', 'Armada', 'Frontier', 'Kicks', 'March', 'Maxima', 'Micra', 'Murano', 'NP300', 'Pathfinder', 'Platina', 'Rogue', 'Sentra', 'Tsuru', 'Urvan', 'Versa', 'X-Trail'],
            'Peugeot' => ['206', '207', '208', '301', '307', '308', '2008', '3008', 'Partner', 'Rifter'],
            'Pontiac' => ['Aztek', 'G3', 'G5', 'Grand Am', 'Sunfire', 'Torrent'],
            'Porsche' => ['911', 'Boxster', 'Cayenne', 'Cayman', 'Macan', 'Panamera'],
            'Ram' => ['700', '1500', '2500', '4000', 'Promaster'],
            'Renault' => ['Clio', 'Duster', 'Kangoo', 'Koleos', 'Kwid', 'Logan', 'Megane', 'Oroch', 'Sandero', 'Stepway'],
            'Seat' => ['Alhambra', 'Altea', 'Arona', 'Ateca', 'Cordoba', 'Ibiza', 'Leon', 'Toledo'],
            'Subaru' => ['Forester', 'Impreza', 'Legacy', 'Outback', 'XV'],
            'Suzuki' => ['Ertiga', 'Grand Vitara', 'Ignis', 'Jimny', 'S-Cross', 'Swift', 'Vitara'],
            'Toyota' => ['4Runner', 'Avanza', 'Camry', 'Corolla', 'Hiace', 'Hilux', 'Land Cruiser', 'Matrix', 'Prius', 'RAV4', 'Sienna', 'Tacoma', 'Tercel', 'Yaris'],
            'Volkswagen' => ['Atlantic', 'Beetle', 'Bora', 'Caribe', 'Combi', 'Derby', 'Golf', 'Jetta', 'Lupo', 'Passat', 'Pointer', 'Polo', 'Saveiro', 'Sedan', 'T-Cross', 'Taos', 'Tiguan', 'Vento'],
            'Volvo' => ['C30', 'S40', 'S60', 'S80', 'V40', 'XC40', 'XC60', 'XC90'],
            'Yamaha' => ['FZ', 'MT-03', 'MT-07', 'R3', 'R6', 'XMAX', 'YBR'],
            'Italika' => ['DM', 'FT', 'RT', 'Vort-X', 'WS', 'X125'],
            'Harley-Davidson' => ['Dyna', 'Iron', 'Road King', 'Softail', 'Sportster', 'Street Glide'],
            'Kenworth' => ['T300', 'T370', 'T600', 'T680', 'T800', 'T880'],
            'Freightliner' => ['Cascadia', 'Columbia', 'M2', 'M2 112', 'Sprinter'],
            'International' => ['4300', '4700', '7600', '9400', 'LoneStar', 'ProStar'],
        ];
    }
}
