
var provincia_list = [
{value: 'Amazonas'},
{value: 'Áncash'},
{value: 'Apurímac'},
{value: 'Arequipa'},
{value: 'Ayacucho'},
{value: 'Cajamarca'},
{value: 'Callao'},
{value: 'Cusco'},
{value: 'Huancavelica'},
{value: 'Huánuco'},
{value: 'Ica'},
{value: 'Junín'},
{value: 'La Libertad'},
{value: 'Lambayeque'},
{value: 'Lima'},
{value: 'Loreto'},
{value: 'Madre de Dios'},
{value: 'Moquegua'},
{value: 'Pasco'},
{value: 'Piura'},
{value: 'Puno'},
{value: 'San Martín'},
{value: 'Tacna'},
{value: 'Tumbes'},
{value: 'Ucayali'},
];

$(document).ready(function() {
   $("#ac_provincia, #ac_provincia2").autocomplete({
      lookup: provincia_list,
   });
});
