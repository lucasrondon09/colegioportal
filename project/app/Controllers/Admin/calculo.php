<?php
namespace App\Controllers\Admin;
use CodeIgniter\Controller;

class calculo extends Controller{
	
	
	
	
	




	public static function get_media_trimestral( $media_provisoria, $nota_formativa ) {
	
		$mt = ( 7 * $media_provisoria + 3 * $nota_formativa ) / 10;
		return $mt;
	
	}

	// Modificações 2020:
    public static function get_media_trimestral__MP_FOR_2020( $media_provisoria, $nota_formativa ) {
        $mt = ( 8 * $media_provisoria + 2 * $nota_formativa ) / 10;
        return $mt;
    }
    public static function get_media_trimestral__S1_FOR_2020($s1, $for) {
        $mt = ( 8 * $s1 + 2 * $for ) / 10;
        return $mt;
    }





    // S1, FOR.
    public static function get_media_trimestral_tipo_1($s1, $for) {

        // média trimestral:
        $mt = ( 7 * $s1 + 3 * $for ) / 10;
        return $mt;
    }

    // 2020:
    // S1, FOR.
    public static function get_media_trimestral_tipo_1_2020($s1, $for) {

        // média trimestral:
        $mt = ( 8 * $s1 + 2 * $for ) / 10;
        return $mt;
    }

    public static function get_media_trimestral_1a3_ano_ingles_2018($s1, $s2, $for) {
		
		// média provisória:
		$mp = ( 7 * $s1 + 7 * $s2 ) / 14;
		
		// média trimestral:
		$mt = calculo::get_media_trimestral($mp, $for);
		
		return $mt;
	}



    // S1, S2, FOR.
    public static function get_media_trimestral_1ano_ingles_e_edfisica_2020($s1, $s2, $for) {

        // média provisória:
        $mp = ( 7 * $s1 + 7 * $s2 ) / 14;

        // média trimestral:
        $mt = calculo::get_media_trimestral__MP_FOR_2020($mp, $for);

        return $mt;
    }



    // S1, S2, FOR.
	public static function get_media_trimestral_tipo_2($s1, $s2, $for) {
	
		// média provisória:
		$mp = ( 7 * $s1 + 7 * $s2 ) / 14;
	
		// média trimestral:
		$mt = calculo::get_media_trimestral($mp, $for);
	
		return $mt;
	}



	// 2020:
    // S1, S2, FOR.
    public static function get_media_trimestral_tipo_2_2020($s1, $s2, $for) {

        // média provisória:
        $mp = ( 7 * $s1 + 7 * $s2 ) / 14;

        // média trimestral:
        $mt = calculo::get_media_trimestral__MP_FOR_2020($mp, $for);

        return $mt;
    }





    // S1, S2, S3, FOR.
	public static function get_media_trimestral_tipo_3($s1, $s2, $s3, $for) {
		
		// média provisória:
		$mp = ( 7 * $s1 + 7 * $s2 + 7 * $s3 ) / 21;
		
		// média trimestral:
		$mt = calculo::get_media_trimestral($mp, $for);
		
		return $mt;
	}



	// 2020:
    // S1, S2, S3, FOR.
    public static function get_media_trimestral_tipo_3_2020($s1, $s2, $s3, $for) {

        // média provisória:
        $mp = ( 7 * $s1 + 7 * $s2 + 7 * $s3 ) / 21;

        // média trimestral:
        $mt = calculo::get_media_trimestral__MP_FOR_2020($mp, $for);

        return $mt;
    }
	
	public static function get_media_trimestral_4a5_ano_portugues_2018($s1, $s2, $s3, $s4, $for) {
		
		// média provisória:
		$mp = ( 7 * $s1 + 7 * $s2 + 7 * $s3 + 7 * $s4 ) / 28;
		
		// média trimestral:
		$mt = calculo::get_media_trimestral($mp, $for);
		
		return $mt;
	}


	// 2020:
    public static function get_media_trimestral_4a5_ano_portugues_2020($s1, $s2, $s3, $s4, $for) {

        // média provisória:
        $mp = ( 7 * $s1 + 7 * $s2 + 7 * $s3 + 7 * $s4 ) / 28;

        // média trimestral:
        $mt = calculo::get_media_trimestral__MP_FOR_2020($mp, $for);

        return $mt;
    }


    public static function get_media_trimestral_6a9_ano_portugues_2018($s1, $s2, $s3, $s4, $sim, $for) {

        // média provisória:
        $mp = ( 7 * $s1 + 7 * $s2 + 7 * $s3 + 7 * $s4 + 3 * $sim ) / 31;

        // média trimestral:
        $mt = calculo::get_media_trimestral($mp, $for);

        return $mt;
    }


    // 2020:
    public static function get_media_trimestral_6a9_ano_portugues_2020($s1, $s2, $s3, $s4, $sim, $for) {

        // média provisória:
        $mp = ( 7 * $s1 + 7 * $s2 + 7 * $s3 + 7 * $s4 + 3 * $sim ) / 31;

        // média trimestral:
        $mt = calculo::get_media_trimestral__MP_FOR_2020($mp, $for);

        return $mt;
    }
	
	
	
	
	
	
	// S1, S2, SIM, FOR.
	public static function get_media_trimestral_tipo_4($s1, $s2, $sim, $for) {

		// média provisória:
		$mp = ( 7 * $s1 + 7 * $s2 + 3 * $sim ) / 17;
		
		// média trimestral:
		$mt = calculo::get_media_trimestral($mp, $for);
		
		return $mt;
		
	}




	// 2020:
    // S1, S2, SIM, FOR.
    public static function get_media_trimestral_tipo_4_2020($s1, $s2, $sim, $for) {

        // média provisória:
        $mp = ( 7 * $s1 + 7 * $s2 + 3 * $sim ) / 17;

        // média trimestral:
        $mt = calculo::get_media_trimestral__MP_FOR_2020($mp, $for);

        return $mt;

    }

	
	// S1, S2, S3, SIM, FOR.
	public static function get_media_trimestral_tipo_5($s1, $s2, $s3, $sim, $for) {
	
	
		// média provisória:
		$mp = ( 7 * $s1 + 7 * $s2 + 7 * $s3 + 3 * $sim ) / 24;

		// média trimestral:
		$mt = calculo::get_media_trimestral($mp, $for);
	
		return $mt;
	
	}


	// 2020:
    public static function get_media_trimestral_tipo_5_2020($s1, $s2, $s3, $sim, $for) {


        // média provisória:
        $mp = ( 7 * $s1 + 7 * $s2 + 7 * $s3 + 3 * $sim ) / 24;

        // média trimestral:
        $mt = calculo::get_media_trimestral__MP_FOR_2020($mp, $for);

        return $mt;
    }


    // 2020:
    public static function get_media_trimestral_tipo_6_2020($s1, $s2, $s3, $s4, $for) {


        // média provisória:
        $mp = ( 7 * $s1 + 7 * $s2 + 7 * $s3 + 7 * $s4 ) / 28;

        // média trimestral:
        $mt = calculo::get_media_trimestral__MP_FOR_2020($mp, $for);

        return $mt;
    }


    // 2020:
    public static function get_media_trimestral_tipo_7_2020($s1, $s2, $s3, $for) {


        // média provisória:
        $mp = ( 7 * $s1 + 7 * $s2 + 7 * $s3) / 21;

        // média trimestral:
        $mt = calculo::get_media_trimestral__MP_FOR_2020($mp, $for);

        return $mt;
    }



    public static function get_media_parcial_anual( $media_1_trimestre, $media_2_trimestre, $media_3_trimestre ) {
	
		// média parcial anual
		$mpa = ( $media_1_trimestre * 3 + $media_2_trimestre * 3 + $media_3_trimestre * 4 ) / 10;
		$mpa = calculo::arredonda_nota($mpa, false);
		return $mpa;
	}
	
	
	public static function get_media_final_anual( $media_parcial_anual, $nota_recuperacao_final ) {
	
		//echo ": $media_parcial_anual , $nota_recuperacao_final";
		
		// média final anual
		$mfa = ( $media_parcial_anual * 4 + $nota_recuperacao_final * 6 ) / 10;
		$mfa = calculo::arredonda_nota($mfa, false);
		return $mfa;
	}
	
	
	
	
	public static function limitar_decimais($nota, $com_virgula = true) {
		$cortado = number_format($nota, 1);
		if($com_virgula ){ 
			return str_replace('.', ',', $cortado);
		}
		return $cortado;
	}




    public static function arredonda_nota($nota, $replace_ponto = true)
    {

        // 		teste 31/03/2017
        /*
        $arredondado = number_format($nota, 1);
        if($replace_ponto) {
            $arredondado = str_replace('.', ',',  number_format($nota, 1));
        }
        return $arredondado;
        */
        // fim teste



        $arredondado = 0.0;


        if($nota < 0.3) {
            $arredondado = 0.0;

        }else if($nota >= 0.3 && $nota < 0.8) {
            $arredondado = 0.5;
        }else {


            for ($i = 0; $i <= 10; $i++) {

                if ($nota > ($i + 0.7) && $nota <= ($i + 1.29)) {
                    $arredondado = $i + 1;
                    break;
                }

                if ($nota > ($i + 1.2) && $nota <= ($i + 1.79)) {
                    $arredondado = $i + 1.5;
                    break;
                }
            }
        }

        if($replace_ponto) {
            $arredondado = str_replace('.', ',',  number_format($arredondado, 1));
        }
        //echo "$nota = $arredondado; ";
        return $arredondado;

    }

	
	
	
	
	
	
	
	
}


?>