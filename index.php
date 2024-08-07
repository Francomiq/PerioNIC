<?php
// Your code here!
// JSON con los datos proporcionados
$json_data = ''
;

// Decodificar el JSON
$data = json_decode($json_data, true);

// Función para obtener valores específicos de dientes y calcular la diferencia PD - GM
function obtenerValoresDientes($numeros_dientes, $data, &$pd_counts, &$pd_gm_counts) {
    $result = [];

    // Iterar sobre cada número de diente
    foreach ($numeros_dientes as $numero_diente) {
        $prefix_gm = 'gm_' . $numero_diente . '_';
        $prefix_pd = 'pd_' . $numero_diente . '_';

        // Determinar los campos según el número de diente
        $fields = [];
        if (in_array((int) $numero_diente, range(41, 48))) {
            $fields = ['db', 'b', 'mb', 'dl', 'l', 'ml']; 
        } elseif (in_array((int) $numero_diente, range(31, 40))) {
            $fields = ['mb', 'b', 'db', 'ml', 'l', 'dl']; 
        } elseif (in_array((int) $numero_diente, range(21, 28))) {
            $fields = ['mb', 'b', 'db', 'mp', 'p', 'dp']; 
        } else {
            // Si no coincide con ningún caso, usar orden por defecto
            $fields = ['db', 'b', 'mb', 'dp', 'p', 'mp'];
        }

        // Inicializar arrays para guardar valores
        $vestibulares = [];
        $palatinos = [];

        // Calcular la diferencia PD - GM y almacenar en los arrays correspondientes
        foreach ($fields as $field) {
            $pd_value = (float) $data[$prefix_pd . $field];
            $gm_value = (float) $data[$prefix_gm . $field];
            $diff = $pd_value - $gm_value;

            // Contar PD en los rangos especificados
            if ($pd_value <= 3 && $pd_value !=0) {
                $pd_counts['<3']++;
            } elseif ($pd_value >= 4 && $pd_value <= 5) {
                $pd_counts['4-5']++;
            } elseif ($pd_value >= 6) {
                $pd_counts['>6']++;
            }

            // Contar PD-GM en los rangos especificados
            if ($diff >= 1 && $diff <= 2) {
                $pd_gm_counts['1-2']++;
            } elseif ($diff >= 3 && $diff <= 4) {
                $pd_gm_counts['3-4']++;
            } elseif ($diff >= 5 && $diff <= 30) {
                $pd_gm_counts['>5']++;
            }

            // Clasificar valores en vestibulares o palatinos según el campo
            if (strpos($field, 'db') !== false || strpos($field, 'b') !== false || strpos($field, 'mb') !== false) {
                $palatinos[] = $diff;
            } else {
                $vestibulares[] = $diff;
            }
        }

        // Formatear los resultados como se requiere
        $result[$numero_diente] = [
            'vestibulares' => implode('-', $vestibulares),
            'palatinos' => implode('-', $palatinos)
        ];
    }

    return $result;
}

// Inicializar contadores
$pd_counts = ['<3' => 0, '4-5' => 0, '>6' => 0];
$pd_gm_counts = ['1-2' => 0, '3-4' => 0, '>5' => 0];

// Ejemplo de uso para obtener valores de varios dientes con diferencias calculadas
$numeros_dientes_superiores = ['18', '17', '16', '15', '14', '13', '12', '11', '21', '22', '23', '24', '25', '26', '27', '28'];
$numeros_dientes_inferiores = ['48', '47', '46', '45', '44', '43', '42', '41', '31', '32', '33', '34', '35', '36', '37', '38'];

$valores_dientes_superiores = obtenerValoresDientes($numeros_dientes_superiores, $data, $pd_counts, $pd_gm_counts);
$valores_dientes_inferiores = obtenerValoresDientes($numeros_dientes_inferiores, $data, $pd_counts, $pd_gm_counts);

// Arrays para almacenar los valores
$vestibulares_superiores = [];
$palatinos_superiores = [];
$vestibulares_inferiores = [];
$linguales_inferiores = [];

// Recolectar los valores para cada tipo
foreach ($numeros_dientes_superiores as $numero_diente) {
    $palatinos_superiores[] = $valores_dientes_superiores[$numero_diente]['vestibulares'];
    $vestibulares_superiores[] = $valores_dientes_superiores[$numero_diente]['palatinos'];
}

foreach ($numeros_dientes_inferiores as $numero_diente) {
    $vestibulares_inferiores[] = $valores_dientes_inferiores[$numero_diente]['vestibulares'];
    $linguales_inferiores[] = $valores_dientes_inferiores[$numero_diente]['palatinos'];
}

// Imprimir resultados en el formato solicitado para dientes superiores
echo "NIC Vestibular | " . implode(" | ", $vestibulares_superiores) . " |<br>";
echo "NIC Palatino | " . implode(" | ", $palatinos_superiores) . " |<br><br>";

// Imprimir resultados en el formato solicitado para dientes inferiores
echo "NIC Lingual | " . implode(" | ", $vestibulares_inferiores) . " |<br>";
echo "NIC Vestibular | " . implode(" | ", $linguales_inferiores) . " |<br>";

// Imprimir conteos de PD
echo "<br>Conteo de PS:<br>";
echo "PS < 3mm: " . $pd_counts['<3'] . "<br>";
echo "PS 4-5mm: " . $pd_counts['4-5'] . "<br>";
echo "PS >o= 6mm: " . $pd_counts['>6'] . "<br>";

// Imprimir conteos de PD-GM
echo "<br>Conteo de NIC:<br>";
echo "NIC 1-2: " . $pd_gm_counts['1-2'] . "<br>";
echo "NIC 3-4: " . $pd_gm_counts['3-4'] . "<br>";
echo "NIC >o= 5: " . $pd_gm_counts['>5'] . "<br>";
?>
