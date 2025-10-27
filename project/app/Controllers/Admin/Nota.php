<?php

namespace App\Controllers\Admin;

use App\Models\Admin\SI_AlunoModel;
use App\Models\Admin\SI_AlunoTurmaModel;
use App\Models\Admin\SI_NivelDisciplinaProvaModel;
use App\Models\Admin\SI_NotaModel;
use App\Models\Admin\SI_TurmaModel;
use App\Models\Admin\SI_FaltaModel;
use App\Models\Admin\SI_PaiModel;
use App\Models\Admin\SI_RecuperacaoModel;
use CodeIgniter\Controller;

class nota extends Controller{
	

	protected $conn;
	
	protected $vetor_todas_notas = array();
	protected $populou_vetor_todas_notas = false;
	
	
	protected $vetor_todas_recuperacao = array();
	protected $populou_vetor_todas_recuperacao = false;
	
	
	protected $vetor_todas_faltas = array();
	protected $populou_vetor_todas_faltas = false;
	
	protected $vetor_retornar_provas_turma = null;
	protected $populou_vetor_retornar_provas_turma = false;
	
	
	protected $populou__disciplina_possui_prova = false;
	
	
	protected $sem_valor;// parametro::nota_sem_valor;
	protected $provas = array (
		's1',
		's2',
		's3',
			's4',
		's',
		'f'
	);
	
	
	protected $vetor_disciplina_possui_prova = null;
	protected $vetor_media_turma = null;
	

	public function __construct() {
		$this->sem_valor = parametro::nota_sem_valor();
		//$this->conn = EASYNC5__model_conn::get_conn();
		
	}
	
	
	
	public function get_provas_turma($turma_id) {
		
		if($this->populou_vetor_retornar_provas_turma) {
			return $this->vetor_retornar_provas_turma;
		}

		
		$result = (new SI_TurmaModel())->query(
												"
												SELECT p.id_prova
												FROM si_turma t
												
												JOIN si_nivel_disciplina_prova p
												ON p.id_nivel = t.id_nivel
												
												WHERE t.id = $turma_id
												AND (
															(t.ano <= 2017 AND p.ano_vigencia = 2017)
														OR	(t.ano >= 2018 AND p.ano_vigencia = t.ano)
													) 
												GROUP BY id_prova
												
												"
												)->getResult();

		$r = (array)$result;
		$this->vetor_retornar_provas_turma = array ();

		foreach ( $this->provas as $ordenado ) {
			// $achou_algum = false;

			foreach ( $r as $ordenar ) {
				if ($ordenar->id_prova == $ordenado) {
					array_push ( $this->vetor_retornar_provas_turma, $ordenado );
					break;
				}
			}
		}
		
		$this->populou_vetor_retornar_provas_turma = true;
		return $this->vetor_retornar_provas_turma;
	}

	public function get_provas_turma_alunos($turma_id) {
		
		if($this->populou_vetor_retornar_provas_turma) {
			return $this->vetor_retornar_provas_turma;
		}

		$result = (new SI_TurmaModel())->query(
												"
												SELECT p.id_prova
												FROM si_turma t
												
												JOIN si_nivel_disciplina_prova p
												ON p.id_nivel = t.id_nivel
												
												WHERE t.id = $turma_id
												AND (
															(t.ano <= 2017 AND p.ano_vigencia = 2017)
														OR	(t.ano >= 2018 AND p.ano_vigencia = t.ano)
													) 
												GROUP BY id_prova
												
												"
												)->getResultArray();

		$r = $result;

		$this->vetor_retornar_provas_turma = array ();

		foreach ( $this->provas as $ordenado ) {
			// $achou_algum = false;

			foreach ( $r as $ordenar ) {
				if ($ordenar['id_prova'] == $ordenado) {
					array_push ( $this->vetor_retornar_provas_turma, $ordenado );
					break;
				}
			}
		}
		
		$this->populou_vetor_retornar_provas_turma = true;
		return $this->vetor_retornar_provas_turma;
	}
	
	private function popular_disciplina_possui_prova() {
		
		if($this->vetor_disciplina_possui_prova == null) {
			
			$q = "
				
			SELECT t.id, id_disciplina, id_prova
			FROM si_nivel_disciplina_prova p
				
			JOIN si_turma t
			ON p.id_nivel = t.id_nivel
			AND (
					(t.ano <= 2017 AND p.ano_vigencia = 2017)
				OR	(t.ano >= 2018 AND p.ano_vigencia = t.ano)
			)
			";
			// echo "$q<br/>";

			$nivelDiscProvaModel = new SI_NivelDisciplinaProvaModel();

			
			$this->vetor_disciplina_possui_prova = $nivelDiscProvaModel->query($q)->getResultArray();

			//dd($this->vetor_disciplina_possui_prova);
		}
		
	}
	
	public function get_disciplina_possui_prova($disciplina, $turma_id, $prova) {
		
		if($this->populou__disciplina_possui_prova == false) {
			$this->popular_disciplina_possui_prova();
			$this->populou__disciplina_possui_prova = true;
		}

		//dd($this->vetor_disciplina_possui_prova );
		foreach ($this->vetor_disciplina_possui_prova as $v) {

			if(
				$v['id'] == $turma_id &&
				$v['id_disciplina'] == $disciplina &&
				$v['id_prova'] == $prova) 
			{
				return true;
			}
		}
		return false;
	}
	
	public function get_vetor_todas_notas($turma_id) {

		if($this->populou_vetor_todas_notas) {
			return $this->vetor_todas_notas;
		}
		

		$result = (new SI_NotaModel())->query(
												"
												SELECT n.id,fk_aluno,fk_turma,trimestre,id_disciplina,id_prova,nota 
												FROM
												si_nota n
												
												JOIN si_turma t
												ON n.fk_turma = t.id
												
												WHERE
												t.id = $turma_id
												

												AND (SELECT COUNT(*) qtde FROM si_aluno_turma WHERE fk_aluno = n.fk_aluno AND fk_turma = n.fk_turma) > 0


												"
												)->getResult();
	
		
		$r = (array)$result;
		$this->vetor_todas_notas = $r;
		$this->populou_vetor_todas_notas = true;
		
		return $this->vetor_todas_notas;
		
	}

	public function get_vetor_todas_notas_alunos($turma_id) {

		if($this->populou_vetor_todas_notas) {
			return $this->vetor_todas_notas;
		}
		

		$result = (new SI_NotaModel())->query(
												"
												SELECT n.id,fk_aluno,fk_turma,trimestre,id_disciplina,id_prova,nota 
												FROM
												si_nota n
												
												JOIN si_turma t
												ON n.fk_turma = t.id
												
												WHERE
												t.id = $turma_id
												

												AND (SELECT COUNT(*) qtde FROM si_aluno_turma WHERE fk_aluno = n.fk_aluno AND fk_turma = n.fk_turma) > 0


												"
												)->getResultArray();
	
		
		$r = $result;

		$this->vetor_todas_notas = $r;
		$this->populou_vetor_todas_notas = true;
		
		return $this->vetor_todas_notas;
		
	}
	
	

	private function get_vetor_todas_recuperacao($turma_id) {

		if($this->populou_vetor_todas_recuperacao) {
			return $this->vetor_todas_recuperacao;
		}
		
		$q = "
		SELECT fk_aluno, id_disciplina, nota
		FROM si_recuperacao
		WHERE fk_turma = $turma_id
		";
		
		$this->vetor_todas_recuperacao = (new SI_RecuperacaoModel())->query($q)->getResultArray();

		$this->populou_vetor_todas_recuperacao = true;
		
		return $this->vetor_todas_recuperacao;
		
	}
	
	private function get_vetor_todas_faltas($turma_id) {
		if($this->populou_vetor_todas_faltas) {
			return $this->vetor_todas_faltas;
		}
		
		
		$q = "
		SELECT 
		id,fk_aluno,fk_turma,trimestre,id_disciplina,faltas
		
		FROM si_falta
		WHERE
		fk_turma = $turma_id
			
		";
		
		
		$r = (new SI_FaltaModel())->query($q)->getResultArray();
		$this->vetor_todas_faltas = $r;
		$this->populou_vetor_todas_faltas = true;
		return $this->vetor_todas_faltas;
		
	}
	
	
	public function get_media_trimestral_periodo_anual($nivel, $aluno_id, $disciplina, $turma_id, $trimestre) {

		
		$todas_notas = $this->get_vetor_todas_notas_alunos( $turma_id );
		$vetor_provas_da_turma = $this->get_provas_turma_alunos ( $turma_id );
		
		$q = (new SI_TurmaModel())->query("select ano from si_turma where id = $turma_id")->getResultArray();
		//$q = "SELECT ano FROM si_turma WHERE id = $turma_id";

		$r = $q;
		$ano = $r[0]['ano'];
				
		$s1 = 0;
		$s2 = 0;
		$s3 = 0;
		$s4 = 0;
		$sim = 0;
		$for = 0;
		
		
		$this->popular_disciplina_possui_prova();
		
		$possui_prova_sem_nota = false;
		foreach ( $vetor_provas_da_turma as $prova ) {
			
			if ($this->get_disciplina_possui_prova ( $disciplina, $turma_id, $prova )) {
				
				
				$nota_encontrada = null;
				
				// buscar a nota do: aluno, turma, trimestre, disciplina e prova especificados:
				foreach ($todas_notas as $nota_obj) {
					
					if(
						$nota_obj['fk_aluno'] == $aluno_id &&
						$nota_obj['fk_turma'] == $turma_id &&
						$nota_obj['trimestre'] == $trimestre &&
						$nota_obj['id_disciplina'] == $disciplina &&
						$nota_obj['id_prova'] == $prova 
						) 
					{
						
						
						//exit();
						$nota_encontrada = $nota_obj['nota'];
						
						//echo "BUSCAR: $aluno_id, $turma_id, $trimestre, $disciplina, $prova;  ENCONTROU: $nota_encontrada<br />";
						
						//echo "(" . $nota_encontrada . ")";
						break;
					}
				}
				
				
				//echo $nota_encontrada . " -- ";
				//exit();
				
				
				if ($nota_encontrada !== null) {
					
					$nota = $nota_encontrada;
					
					if ($prova == 's1') {
						$s1 = $nota;
					}
					if ($prova == 's2') {
						$s2 = $nota;
					}
					if ($prova == 's3') {
						$s3 = $nota;
					}
					if ($prova == 's4') {
						$s4 = $nota;
					}
					if ($prova == 's') {
						$sim = $nota;
					}
					if ($prova == 'f') {
						$for = $nota;
					}
				} else {
					$possui_prova_sem_nota = true;
				}
			}else{
				//echo "NÃO POSSUI $disciplina, $turma_id, $prova<br />";
			}
		}
		
		//echo "<pre>$nivel, $disciplina, $s1, $s2, $s3, $sim, $for;</pre>";
		
		// $media_trimestral = 0;
		$media_trimestral_arredondada = 0;
		
		$texto_media_trimestral = '';
		
		if ($possui_prova_sem_nota) {
			// não é possível calcular a média trimestral,
			// pois existem provas sem nota.
			$texto_media_trimestral = $this->sem_valor;
		} else {
			
			// exibir o cálculo da média trimestral.
			
			
			$mt = $this->get_media_trimestral ( $nivel, $disciplina, $s1, $s2, $s3, $s4, $sim, $for, $ano );
			
			//echo "CONSULTA: $nivel, $aluno_id, $disciplina, $turma_id, $trimestre<br />";
			//echo "MT =get_media_trimestral -> $nivel, $disciplina, $s1, $s2, $s3, $sim, $for ------ ".sizeof($todas_notas)."<br /><br />";
			
			//echo "<pre>--- Média Trimestral ALUNO ID ($aluno_id, DISCIPLINA $disciplina, TURMA $turma_id, TRIM $trimestre ------- " . $mt . " -.- </pre>";
			$media_trimestral_arredondada = calculo::arredonda_nota ( $mt );
			$texto_media_trimestral = "$media_trimestral_arredondada";
		}
		return $texto_media_trimestral;
	}
	
	
	public function get_media_trimestral($nivel, $disciplina, $s1, $s2, $s3, $s4, $sim, $for, $ano = -1) {


        if($ano == 2020) {
            return $this->get_media_trimestral_2020($nivel, $disciplina, $s1, $s2, $s3, $s4, $sim, $for);
        }

        if($ano >= 2021) {
            return $this->get_media_trimestral_2021_em_diante($nivel, $disciplina, $s1, $s2, $s3, $s4, $sim, $for);
        }

		$mt = 0;
		
		if ($nivel == '1a') {
			// todas disciplinas usam tipo 1
			$mt = calculo::get_media_trimestral_tipo_1 ( $s1, $for );
		}
		
		if ($nivel == '2a' || $nivel == '3a') {
			if ($disciplina == 'p' || $disciplina == 'ma' || $disciplina == 'h' || $disciplina == 'g' || $disciplina == 'c') {
				$mt = calculo::get_media_trimestral_tipo_2 ( $s1, $s2, $for );
			} else {
				$mt = calculo::get_media_trimestral_tipo_1 ( $s1, $for );
			}
		}
	
		
		
		
		
		// 2018 - NOVA regra de português do 4º ao 9º ANO campo novo S4:
		if($ano >= 2018 && $disciplina == 'p') {
			if($nivel == '4a' || $nivel == '5a') {
				$mt = calculo::get_media_trimestral_4a5_ano_portugues_2018 ( $s1, $s2, $s3, $s4, $for );
				return $mt;
			}
			if($nivel == '6a' || $nivel == '7a' || $nivel == '8a' || $nivel == '9a') {
				$mt = calculo::get_media_trimestral_6a9_ano_portugues_2018 ( $s1, $s2, $s3, $s4, $sim, $for );
				return $mt;
			}
		}
		
		// 2018 - NOVA regra de português do 1º ao 3º ANO campo S2 INGLÊS:
		if($ano >= 2018 && $disciplina == 'l') {
			if($nivel == '1a' || $nivel == '2a' || $nivel == '3a') {
				$mt = calculo::get_media_trimestral_1a3_ano_ingles_2018 ( $s1, $s2, $for );
				return $mt;
			}
		}
		
		
		
		
		
		

		if ($nivel == '4a' || $nivel == '5a') {
			if ($disciplina == 'p' || $disciplina == 'ma') {
				
				$mt = calculo::get_media_trimestral_tipo_3 ( $s1, $s2, $s3, $for );
				
				// echo "$s1, $s2, $s3, $for";
			} else if ($disciplina == 'h' || $disciplina == 'g' || $disciplina == 'c' || $disciplina == 'l') {
				$mt = calculo::get_media_trimestral_tipo_2 ( $s1, $s2, $for );
			} else {
				$mt = calculo::get_media_trimestral_tipo_1 ( $s1, $for );
			}
		}
		
		
		if ($nivel == '6a' || $nivel == '7a' || $nivel == '8a') {
			if ($disciplina == 'p' || $disciplina == 'ma') {
				$mt = calculo::get_media_trimestral_tipo_5 ( $s1, $s2, $s3, $sim, $for );
			} else if ($disciplina == 'h' || $disciplina == 'g' || $disciplina == 'l' || $disciplina == 'c') {
				$mt = calculo::get_media_trimestral_tipo_4 ( $s1, $s2, $sim, $for );
			} else if ($disciplina == 'e') {
				$mt = calculo::get_media_trimestral_tipo_2 ( $s1, $s2, $for );
			} else {
				$mt = calculo::get_media_trimestral_tipo_1 ( $s1, $for );
			}
		}
		
		
		if ($nivel == '9a') {
			if ($disciplina == 'p' || $disciplina == 'ma' || $disciplina == 'c') {
				$mt = calculo::get_media_trimestral_tipo_5 ( $s1, $s2, $s3, $sim, $for );
			} else if ($disciplina == 'h' || $disciplina == 'g' || $disciplina == 'l') {
				$mt = calculo::get_media_trimestral_tipo_4 ( $s1, $s2, $sim, $for );
			} else if ($disciplina == 'e') {
				$mt = calculo::get_media_trimestral_tipo_2 ( $s1, $s2, $for );
			} else {
				$mt = calculo::get_media_trimestral_tipo_1 ( $s1, $for );
			}
		}
		
		return $mt;
	}


    public function get_media_trimestral_2021_em_diante($nivel, $disciplina, $s1, $s2, $s3, $s4, $sim, $for)
    {

//            'p' => 'Português',
//			'ma' => 'Matemática',
//			'h' => 'História',
//			'g' => 'Geografia',
//			'c' => 'Ciências',
//			'l' => 'Língua Estrangeira',
//			'f' => 'Filosofia',
//			'e' => 'Educação física',
//			'mu' => 'Música',
//			'a' => 'Artes'


        $mt = 0;

        if ($nivel == '1a') {
            if ($disciplina == 'p' || $disciplina == 'ma' || $disciplina == 'c' || $disciplina == 'h' || $disciplina == 'g' || $disciplina == 'f' || $disciplina == 'mu' || $disciplina == 'a') {
                $mt = calculo::get_media_trimestral_tipo_1_2020( $s1, $for );
            } else {
                // Inglês / Ed. física:
                $mt = calculo::get_media_trimestral_1ano_ingles_e_edfisica_2020( $s1, $s2, $for );
            }
        }

        if ($nivel == '2a' || $nivel == '3a') {
            if ($disciplina == 'p' || $disciplina == 'ma' || $disciplina == 'h' || $disciplina == 'g' || $disciplina == 'c' || $disciplina == 'l' || $disciplina == 'e') {
                $mt = calculo::get_media_trimestral_tipo_2_2020( $s1, $s2, $for );
            } else {
                // Filosofia / Música / Artes:
                $mt = calculo::get_media_trimestral_tipo_1_2020( $s1, $for );
            }
        }



        if ($nivel == '4a' || $nivel == '5a') {

            if($disciplina == 'p') {
                $mt = calculo::get_media_trimestral_tipo_6_2020($s1, $s2, $s3, $s4, $for);

            }else if($disciplina == 'ma') {
                $mt = calculo::get_media_trimestral_tipo_3_2020($s1, $s2, $s3, $for);

            } else if ($disciplina == 'h' || $disciplina == 'g' || $disciplina == 'c' || $disciplina == 'l' || $disciplina == 'e') {
                $mt = calculo::get_media_trimestral_tipo_2_2020( $s1, $s2, $for );

            } else if ($disciplina == 'f' || $disciplina == 'mu' || $disciplina == 'a') {
                $mt = calculo::get_media_trimestral_tipo_1_2020($s1, $for);

            }
        }


        if ($nivel == '6a' || $nivel == '7a' || $nivel == '8a' || $nivel == '9a') {
            if ($disciplina == 'p') {
                $mt = calculo::get_media_trimestral_6a9_ano_portugues_2020 ( $s1, $s2, $s3, $s4, $sim, $for );

            } else if ($disciplina == 'ma' || $disciplina == 'c') {
                $mt = calculo::get_media_trimestral_tipo_5_2020 ( $s1, $s2, $s3, $sim, $for );

            }else if ($disciplina == 'h' || $disciplina == 'g' || $disciplina == 'l') {
                $mt = calculo::get_media_trimestral_tipo_4_2020( $s1, $s2, $sim, $for );
            } else if ($disciplina == 'e') {
                $mt = calculo::get_media_trimestral_tipo_2_2020( $s1, $s2, $for );
            } else {
                // filosofia / música / artes
                $mt = calculo::get_media_trimestral_tipo_1 ( $s1, $for );

                // atualização: aqui também vai cair a disciplina 'd': Educação Digital.
            }
        }

        return $mt;
    }


    public function get_media_trimestral_2020($nivel, $disciplina, $s1, $s2, $s3, $s4, $sim, $for)
    {

//            'p' => 'Português',
//			'ma' => 'Matemática',
//			'h' => 'História',
//			'g' => 'Geografia',
//			'c' => 'Ciências',
//			'l' => 'Língua Estrangeira',
//			'f' => 'Filosofia',
//			'e' => 'Educação física',
//			'mu' => 'Música',
//			'a' => 'Artes'


        $mt = 0;

        if ($nivel == '1a') {
            if ($disciplina == 'p' || $disciplina == 'ma' || $disciplina == 'c' || $disciplina == 'h' || $disciplina == 'g' || $disciplina == 'f' || $disciplina == 'mu' || $disciplina == 'a') {
                $mt = calculo::get_media_trimestral_tipo_1_2020( $s1, $for );
            } else {
                // Inglês / Ed. física:
                $mt = calculo::get_media_trimestral_1ano_ingles_e_edfisica_2020( $s1, $s2, $for );
            }
        }

        if ($nivel == '2a' || $nivel == '3a') {
            if ($disciplina == 'p' || $disciplina == 'ma' || $disciplina == 'h' || $disciplina == 'g' || $disciplina == 'c' || $disciplina == 'l' || $disciplina == 'e') {
                $mt = calculo::get_media_trimestral_tipo_2_2020( $s1, $s2, $for );
            } else {
                // Filosofia / Música / Artes:
                $mt = calculo::get_media_trimestral_tipo_1_2020( $s1, $for );
            }
        }



        if ($nivel == '4a' || $nivel == '5a') {

            if($disciplina == 'p') {
                $mt = calculo::get_media_trimestral_tipo_6_2020($s1, $s2, $s3, $s4, $for);

            }else if($disciplina == 'ma') {
                $mt = calculo::get_media_trimestral_tipo_3_2020($s1, $s2, $s3, $for);

            } else if ($disciplina == 'h' || $disciplina == 'g' || $disciplina == 'c' || $disciplina == 'l') {
                $mt = calculo::get_media_trimestral_tipo_2_2020( $s1, $s2, $for );

            } else if ($disciplina == 'f' || $disciplina == 'mu' || $disciplina == 'a' || $disciplina == 'e') {
                $mt = calculo::get_media_trimestral_tipo_1_2020($s1, $for);

            }
        }


        if ($nivel == '6a' || $nivel == '7a' || $nivel == '8a' || $nivel == '9a') {
            if ($disciplina == 'p') {
                $mt = calculo::get_media_trimestral_6a9_ano_portugues_2020 ( $s1, $s2, $s3, $s4, $sim, $for );

            } else if ($disciplina == 'ma') {
                $mt = calculo::get_media_trimestral_tipo_5_2020 ( $s1, $s2, $s3, $sim, $for );

            }else if ($disciplina == 'h' || $disciplina == 'g' || $disciplina == 'l' || $disciplina == 'c') {
                $mt = calculo::get_media_trimestral_tipo_4_2020( $s1, $s2, $sim, $for );
            } else if ($disciplina == 'e') {
                $mt = calculo::get_media_trimestral_tipo_2_2020( $s1, $s2, $for );
            } else {
                // filosofia / música / artes
                $mt = calculo::get_media_trimestral_tipo_1 ( $s1, $for );

                // atualização: aqui também vai cair a disciplina 'd': Educação Digital.
            }
        }

        return $mt;
    }


	public function get_nota_recuperacao($aluno_id, $turma_id, $disciplina) {

		$aluno_id = is_array($aluno_id) ? $aluno_id['aluno'] : $aluno_id;
		/*
		$q = "
		SELECT nota
		FROM si_recuperacao
		WHERE
		fk_aluno = $aluno_id
		AND fk_turma = $turma_id
		AND id_disciplina = '$disciplina'";
		
		$r = $this->conn->qcv ( $q, "nota" );
		
		if ($r != null) {
			return $r [0];
		}
		return $this->sem_valor;
		
		*/
		
		$vetor = $this->get_vetor_todas_recuperacao($turma_id);
		
		// "fk_aluno,id_disciplina,nota");
		
		foreach ($vetor as $v) {
			if(
				$v['fk_aluno'] == $aluno_id &&
				$v['id_disciplina'] == $disciplina
			) {
				return $v['nota'];
			}
		}
		return $this->sem_valor;
	}
	
	
	public function boletim_anual_aluno($aluno_id, $turma_id, $vetor_media_da_turma) {


		$aluno_id = is_array($aluno_id) ? $aluno_id['aluno'] : $aluno_id;
		
		
		/*
		 * 
		 * 
		 * $vetor_media_da_turma = 
			 ['1t']['p'] = '8.0'
			 ['1t']['ma'] = '8.0'
			 ['2t']['p'] = '8.0'
			 ['2t']['ma'] = '8.0'
		 * 
		 */
		
		
		$this->popular_disciplina_possui_prova();

		
		
		
		$media_nucl_comum_ALUNO = $this->media_nucleo_comum_ALUNO($aluno_id, $turma_id);
		$media_geral_ALUNO = $this->media_geral_ALUNO($aluno_id, $turma_id);

		

		
		/*
		echo '<pre>';
		print_r($media_nucl_comum_ALUNO);
		exit();
		*/
		
		$obj_aluno = (new SI_AlunoModel())->find($aluno_id);
		$obj_turma = (new SI_TurmaModel())->find($turma_id);

		
		$nivel = $obj_turma->id_nivel;
		
		$vetor_provas_da_turma = $this->get_provas_turma ( $turma_id );
		/*
		 * echo 'Aluno: <b>' . $obj_aluno->getNome () . '</b><br /> Turma selecionada: <span style="font-size:18px;">' . $obj_turma->getNome () . '</span> &nbsp; &nbsp; &nbsp; Período: <span style="font-size:18px;">anual</span> &nbsp; &nbsp; &nbsp; ano: <span style="font-size:18px;">' . $obj_turma->getAno () . '</span><br/><br/>';
		 */
		$disciplinas = parametro::disciplinas();
		
		$vetor_notas_trimestrais_todas_disciplinas = array ();
		// $tabela = new modelo__tabela ();
		
		$vetor_colunas = array (
			'-',
			'1º Trim',
			'Faltas',
			'2º Trim',
			'Faltas',
			'3º Trim',
			'Faltas',
			'MPA',
			'REC',
			'MA' 
		);
		// $tabela->adicionar_colunas ( $vetor_colunas );
		
		$notas_trimestrais___todas_disciplinas = array();
		
		foreach( $disciplinas as $disciplina => $nome_disciplina ) {
			
			// for($i=1; $i<=3, $i++) {
			
			// $vetor_linha = array ();
			// array_push ( $vetor_linha, "<b>$nome_disciplina</b>" );
			$vetor_notas_trimestrais = array ();
			$vetor_notas_trimestrais ["cod_disciplina"] = $disciplina;
			$vetor_notas_trimestrais ["disciplina"] = $nome_disciplina;
			
			$notas_trimestrais = array ();
			
			
			
			$trimestre = 1;
			$texto_media_trimestral = $this->get_media_trimestral_periodo_anual ( $nivel, $aluno_id, $disciplina, $turma_id, $trimestre );
			array_push ( $notas_trimestrais, str_replace ( ",", ".", $texto_media_trimestral ) );
			
			$vetor_notas_trimestrais ["1t"] = $texto_media_trimestral;
			$vetor_notas_trimestrais ["1tf"] = $this->get_faltas ( $aluno_id, $turma_id, $disciplina, $trimestre );
			
			if($vetor_media_da_turma == null) {
				throw new Exception("\$vetor_media_da_turma NÃO DEFINIDO.");
				//$vetor_notas_trimestrais ["1t_media_turma"] = $this->get_media_turma ( $turma_id, $disciplina, $trimestre );
			}else{
				$vetor_notas_trimestrais ["1t_media_turma"] = $vetor_media_da_turma['1t'][$disciplina];
			}
			
			
			
			$trimestre = 2;
			$texto_media_trimestral = $this->get_media_trimestral_periodo_anual ( $nivel, $aluno_id, $disciplina, $turma_id, $trimestre );
			array_push ( $notas_trimestrais, str_replace ( ",", ".", $texto_media_trimestral ) );
			
			$vetor_notas_trimestrais ["2t"] = $texto_media_trimestral;
			$vetor_notas_trimestrais ["2tf"] = $this->get_faltas ( $aluno_id, $turma_id, $disciplina, $trimestre );
			
			if($vetor_media_da_turma == null) {
				throw new Exception("\$vetor_media_da_turma NÃO DEFINIDO.");
				//$vetor_notas_trimestrais ["2t_media_turma"] = $this->get_media_turma ( $turma_id, $disciplina, $trimestre );
				
			}else{
				$vetor_notas_trimestrais ["2t_media_turma"] = $vetor_media_da_turma['2t'][$disciplina];
				
			}
			
			
			
			$trimestre = 3;
			$texto_media_trimestral = $this->get_media_trimestral_periodo_anual ( $nivel, $aluno_id, $disciplina, $turma_id, $trimestre );
			
			array_push ( $notas_trimestrais, str_replace ( ",", ".", $texto_media_trimestral ) );
			
			$vetor_notas_trimestrais ["3t"] = $texto_media_trimestral;
			$vetor_notas_trimestrais ["3tf"] = $this->get_faltas ( $aluno_id, $turma_id, $disciplina, $trimestre );
			
			if($vetor_media_da_turma == null) {
				throw new Exception("\$vetor_media_da_turma NÃO DEFINIDO.");
				//$vetor_notas_trimestrais ["3t_media_turma"] = $this->get_media_turma ( $turma_id, $disciplina, $trimestre );
				
			}else{
				$vetor_notas_trimestrais ["3t_media_turma"] = $vetor_media_da_turma['3t'][$disciplina];
				
			}
			
			
			
			$notas_trimestrais___todas_disciplinas[$disciplina] = $notas_trimestrais;
			
			
			$possui_todas_notas_trimestrais = true;
			foreach ( $notas_trimestrais as $nota ) {
				if ($nota == $this->sem_valor) {
					// não tem uma nota trimestral.
					$possui_todas_notas_trimestrais = false;
					break;
				}
			}
			
			$mpa = $this->sem_valor;
			$mpa_formatada = $this->sem_valor;
			
			if ($possui_todas_notas_trimestrais) {
				$mpa = calculo::get_media_parcial_anual ( $notas_trimestrais [0], $notas_trimestrais [1], $notas_trimestrais [2] );
				
				// convertendo de inteiro para decimal (para exibir as casas decimais, mesmo que seja inteiro):
				$mpa_formatada = calculo::arredonda_nota ( $mpa );
				$mpa_formatada = str_replace ( ".", ",", $mpa_formatada );
			}
			
			$rec = $this->get_nota_recuperacao ( $aluno_id, $turma_id, $disciplina );
			$rec_formatada = str_replace ( ".", ",", $rec );
			
			$ma = $this->sem_valor;
			
			if ($mpa != $this->sem_valor) {
				
				if ($mpa >= 6) {
					// não precisa de recuperação.
					$rec_formatada = "-";
					$ma = $mpa;
				} else {
					// precisa da nota da recuperação para calcular a MA:
					if ($rec != $this->sem_valor) {
						
						// calcular ma:
						$ma = calculo::get_media_final_anual ( $mpa, $rec );
					} else {
						// precisa da recuperação, mas não lançou a recuperação...
						$ma = $this->sem_valor;
					}
				}
			}
			
			if ($ma == $this->sem_valor) {
				$ma_formatada = $this->sem_valor;
			} else {
				$ma_formatada = calculo::arredonda_nota ( $ma );
				
				// tenho q saber a MA arredondada.
				$ma = str_replace ( ",", ".", $ma_formatada );
				$ma_formatada = str_replace ( ".", ",", $ma_formatada );
			}
			
			/*
			 * array_push ( $vetor_linha, $mpa); array_push ( $vetor_linha, $rec_formatada); array_push ( $vetor_linha, $ma_formatada);
			 */
			
			$vetor_notas_trimestrais ['mpa'] = $mpa_formatada;
			$vetor_notas_trimestrais ['rec'] = $rec_formatada;
			$vetor_notas_trimestrais ['ma']  = $ma_formatada;
			
			// $tabela->adiciona_linha ( $vetor_linha );
			array_push ( $vetor_notas_trimestrais_todas_disciplinas, $vetor_notas_trimestrais );
		}
		
		//print_r($notas_trimestrais___todas_disciplinas);
		
		
		$possui_nota_em_todas_materias_todos_trimestres = true;
		$possui_nota_ma_abaixo_da_media = false;
		
		foreach ($vetor_notas_trimestrais_todas_disciplinas as $v) {
			//print_r($v['ma']);
			
			$ma_numero = str_replace(",", ".", $v['ma']);
			
			if($v['ma'] == parametro::nota_sem_valor()) {
				// faltou alguma nota.
				$possui_nota_em_todas_materias_todos_trimestres = false;
				$nota_faltante = @$k;
				// possui nota que não foi lançada.
				break;
			}else if($ma_numero < 6) {
				$possui_nota_ma_abaixo_da_media = true;
				// aluno reprovado.
				break;
			}
		}
		
		
		
		
		
		
		$aluno = (new SI_AlunoModel())->find($aluno_id);
		
		$vetor_resposta = array ();
		$vetor_resposta ["nome"] = $aluno->nome;
		$vetor_resposta ["matricula"] = $aluno->matricula;
		
		$vetor_resposta ["nome_turma"] = $obj_turma->nome;
		$vetor_resposta ["grau"] = parametro::get_grau( $obj_turma->id_grau);
		$vetor_resposta ["ano"] = $obj_turma->ano;
		$vetor_resposta ["turno"] = parametro::get_periodo($obj_turma->id_periodo);
		
		$vetor_resposta ["dt_nasc"] = $aluno->nasc;
		$vetor_resposta ["cid_nasc"] = $aluno->cid_nasc;
		$vetor_resposta ["uf_nasc"] = $aluno->uf;
		
		$vetor_resposta ["nome_pai"] = (new SI_PaiModel())->find($aluno->fk_pai)->nome_pai;
		$vetor_resposta ["nome_mae"] = (new SI_PaiModel())->find($aluno->fk_pai)->nome_mae;
		
		//$r = $this->conn->qcv("SELECT NOW() data_atual", "data_atual");
		//$vetor_resposta ["data_atual"] = data::formato1($r[0]);
		$vetor_resposta ["data_atual"] =  date('d/m/Y');;
		
		
		
		$vetor_resposta ["aluno_aprovado_ficha_individual"] = '?';
		if($possui_nota_em_todas_materias_todos_trimestres) {
			if($possui_nota_ma_abaixo_da_media == false) {
				// aprovado.
				$vetor_resposta ["aluno_aprovado_ficha_individual"] = 'APROVADO';
			}else{
				// reprovado.
				$vetor_resposta ["aluno_aprovado_ficha_individual"] = 'REPROVADO';
			}
		}else{
			// não lançou alguma nota.
			$vetor_resposta ["aluno_aprovado_ficha_individual"] = 'EXISTEM NOTAS PENDENTES';
		}
		
		

		$vetor_resposta ["media_nucleo_comum_1trim"] = $media_nucl_comum_ALUNO["1t"];
		$vetor_resposta ["media_nucleo_comum_2trim"] = $media_nucl_comum_ALUNO["2t"];
		$vetor_resposta ["media_nucleo_comum_3trim"] = $media_nucl_comum_ALUNO["3t"];
		
		
		
		$vetor_resposta ["media_geral_1trim"] = $media_geral_ALUNO["1t"];
		$vetor_resposta ["media_geral_2trim"] = $media_geral_ALUNO["2t"];
		$vetor_resposta ["media_geral_3trim"] = $media_geral_ALUNO["3t"];
		
		
		
		$vetor_resposta ["notas"] = $vetor_notas_trimestrais_todas_disciplinas;
		
		
		/*
		echo '<pre>';
		print_r($vetor_resposta);
		*/
		//exit();
		return $vetor_resposta;
	}
	
	
	
	
	
	
	
	
	
	
	
	// MÉDIA GERAL - ALUNO (todas disciplinas)
	protected function media_geral_ALUNO($aluno_id, $turma_id) {
	
		//$pdf = new visao_pagina_conteudo_relatorio__media_individual__nucleo_comum__pdf();
		$aluno_id = is_array($aluno_id) ? $aluno_id['aluno'] : $aluno_id;
		
		if($turma_id == 0) {
			//$turma_id = EASYNC5__si_aluno_turma::getByPK((int)util::GET('aluno_turma_id'))->getFk_turma();
		}
		
		$turma = (new SI_TurmaModel())->find($turma_id);
	
	
		$disciplinas = parametro::disciplinas(false);
	
		$q = "
	
		SELECT a.id, matricula, a.nome
		FROM si_aluno a
		
		WHERE a.id = $aluno_id
		";
	
		$r = (new SI_TurmaModel())->query($q)->getResultArray();
		
		//$nota = new nota();
	
		$qtde_disciplinas = sizeof($disciplinas);

		$vetor_resposta = array();
		$vetor_alunos = array();

		foreach($r as $v) {
		
			$aluno_id = $v['id'];
				
			$soma_media_1trim_todas_disc = 0.0;
			$soma_media_2trim_todas_disc = 0.0;
			$soma_media_3trim_todas_disc = 0.0;
	
		
			for($trimestre=1; $trimestre<=3; $trimestre++) {
				$nota_trim = 0;
				foreach ($disciplinas as $disciplina => $nome_disc) {
					$possui_nota = true;
			
					$nota_trim = $this->get_media_trimestral_periodo_anual($turma->id_nivel, $aluno_id, $disciplina, $turma_id, $trimestre);
			
					if($nota_trim == parametro::nota_sem_valor()) {
						// não tem nota para esta disciplina.
							
						$possui_nota = false;
							break;
					}
					if($possui_nota) {
				
						$nota_trim = str_replace(",", ".", $nota_trim);
							
							
						if($trimestre == 1) {
							$soma_media_1trim_todas_disc += $nota_trim;
				
						}else if($trimestre == 2) {
							$soma_media_2trim_todas_disc += $nota_trim;
				
						}else if($trimestre == 3) {
							$soma_media_3trim_todas_disc += $nota_trim;
				
						}
					}else{
						if($trimestre == 1) {
						$soma_media_1trim_todas_disc = 0;
						}
						if($trimestre == 2) {
						$soma_media_2trim_todas_disc = 0;
						}
						if($trimestre == 3) {
						$soma_media_3trim_todas_disc = 0;
						}
					}
				}
			}
	
			$vetor_aluno = array();

	
			$vetor_aluno['matricula'] = $v['matricula'];
			$vetor_aluno['nome'] = $v['nome'];
			$vetor_aluno['1t'] = number_format( $soma_media_1trim_todas_disc / $qtde_disciplinas, 1 );
			$vetor_aluno['2t'] = number_format( $soma_media_2trim_todas_disc / $qtde_disciplinas, 1 );
			$vetor_aluno['3t'] = number_format( $soma_media_3trim_todas_disc / $qtde_disciplinas, 1 );

			return $vetor_aluno;
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	// MÉDIA NÚCLEO COMUM - ALUNO
	protected function media_nucleo_comum_ALUNO($aluno_id, $turma_id) {

		//$pdf = new visao_pagina_conteudo_relatorio__media_individual__nucleo_comum__pdf();
	
		$aluno_id = is_array($aluno_id) ? $aluno_id['aluno'] : $aluno_id;
		$turma_id = $turma_id;
		
		if($turma_id == 0) {
		
			//$turma_id = EASYNC5__si_aluno_turma::getByPK((int)util::GET('aluno_turma_id'))->getFk_turma();
		}
		
		$turma = (new SI_TurmaModel())->find($turma_id);
		//$turma = EASYNC5__si_turma::getByPK($turma_id);
	
	
		$disciplinas = parametro::disciplinas(true);
	
		$q = (new SI_AlunoModel())->query("
											SELECT a.id, matricula, a.nome
											FROM si_aluno a
											
											WHERE a.id = $aluno_id
											")->getResultArray();
	
		$r = $q;

		
		//$nota = new nota();
		
	
	
		$qtde_disciplinas = sizeof($disciplinas);

		$vetor_resposta = array();
		$vetor_alunos = array();
		
		foreach($r as $v) {
		
			$aluno_id = $v['id'];
				
			$soma_media_1trim_todas_disc = 0.0;
			$soma_media_2trim_todas_disc = 0.0;
			$soma_media_3trim_todas_disc = 0.0;
	
		
			for($trimestre=1; $trimestre<=3; $trimestre++) {
				$nota_trim = 0;
				foreach ($disciplinas as $disciplina => $nome_disc) {
					$possui_nota = true;
			
					$nota_trim = $this->get_media_trimestral_periodo_anual($turma->id_nivel, $aluno_id, $disciplina, $turma_id, $trimestre);
			
					if($nota_trim == parametro::nota_sem_valor()) {
						// não tem nota para esta disciplina.
							
						$possui_nota = false;
							break;
					}
					if($possui_nota) {
				
						$nota_trim = str_replace(",", ".", $nota_trim);
							
							
						if($trimestre == 1) {
							$soma_media_1trim_todas_disc += $nota_trim;
				
						}else if($trimestre == 2) {
							$soma_media_2trim_todas_disc += $nota_trim;
				
						}else if($trimestre == 3) {
							$soma_media_3trim_todas_disc += $nota_trim;
				
						}
					}else{
						if($trimestre == 1) {
						$soma_media_1trim_todas_disc = 0;
						}
						if($trimestre == 2) {
						$soma_media_2trim_todas_disc = 0;
						}
						if($trimestre == 3) {
						$soma_media_3trim_todas_disc = 0;
						}
					}
				}
			}
	
			$vetor_aluno = array();
	
			$vetor_aluno['matricula'] = $v['matricula'];
			$vetor_aluno['nome'] = $v['nome'];
			$vetor_aluno['1t'] = number_format( $soma_media_1trim_todas_disc / $qtde_disciplinas, 1 );
			$vetor_aluno['2t'] = number_format( $soma_media_2trim_todas_disc / $qtde_disciplinas, 1 );
			$vetor_aluno['3t'] = number_format( $soma_media_3trim_todas_disc / $qtde_disciplinas, 1 );

			return $vetor_aluno;
		}
	}
	



	// MÉDIA NÚCLEO COMUM - TURMA
	protected function media_nucleo_comum_turma($turma_id) {
	
		$pdf = new visao_pagina_conteudo_relatorio__media_individual__nucleo_comum__pdf();
	
	
		$turma_id = (int)util::GET('turma_id');
		$turma = EASYNC5__si_turma::getByPK($turma_id);
	
	
		$disciplinas = parametro::disciplinas(true);
	
		$q = "
	
		SELECT a.id, matricula, a.nome
		FROM si_aluno a
	
		JOIN si_aluno_turma at
		ON a.id = at.fk_aluno
	
		JOIN si_turma t
		ON at.fk_turma = t.id
	
		WHERE
		a.status = '1'
		AND t.status = '1'
		AND t.id = $turma_id
	
		ORDER BY a.nome
	
		";
	
		$r = $this->conn->qcv($q, "id,matricula,nome");
	
		//$nota = new nota();
	
	
	
		$qtde_disciplinas = sizeof($disciplinas);
	
		$vetor_resposta = array();
		$vetor_alunos = array();
	
		foreach($r as $v) {
	
		$aluno_id = $v[0];
	
			$soma_media_1trim_todas_disc = 0.0;
			$soma_media_2trim_todas_disc = 0.0;
			$soma_media_3trim_todas_disc = 0.0;
	
	
			for($trimestre=1; $trimestre<=3; $trimestre++) {
			$nota_trim = 0;
			foreach ($disciplinas as $disciplina => $nome_disc) {
			$possui_nota = true;
				
			$nota_trim = $this->get_media_trimestral_periodo_anual($turma->getId_nivel(), $aluno_id, $disciplina, $turma_id, $trimestre);
				
			if($nota_trim == parametro::nota_sem_valor()) {
			// não tem nota para esta disciplina.
				
			$possui_nota = false;
			break;
			}
			if($possui_nota) {
	
			$nota_trim = str_replace(",", ".", $nota_trim);
				
				
			if($trimestre == 1) {
			$soma_media_1trim_todas_disc += $nota_trim;
	
			}else if($trimestre == 2) {
			$soma_media_2trim_todas_disc += $nota_trim;
	
			}else if($trimestre == 3) {
			$soma_media_3trim_todas_disc += $nota_trim;
	
			}
			}else{
			if($trimestre == 1) {
			$soma_media_1trim_todas_disc = 0;
			}
			if($trimestre == 2) {
			$soma_media_2trim_todas_disc = 0;
			}
				if($trimestre == 3) {
					$soma_media_3trim_todas_disc = 0;
				}
			}
			}
			}
	
			$vetor_aluno = array();
	
			$vetor_aluno['matricula'] = $v[1];
			$vetor_aluno['nome'] = $v[2];
			$vetor_aluno['1t'] = number_format( $soma_media_1trim_todas_disc / $qtde_disciplinas, 1 );
			$vetor_aluno['2t'] = number_format( $soma_media_2trim_todas_disc / $qtde_disciplinas, 1 );
					$vetor_aluno['3t'] = number_format( $soma_media_3trim_todas_disc / $qtde_disciplinas, 1 );
	
					array_push($vetor_alunos, $vetor_aluno);
	}
	$vetor_resposta['turma'] = $turma->getNome() . " - " . $turma->getAno();
	return $vetor_alunos;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	private function get_media_nucleo_comum_1trim() {
		throw Exception("Não implementado");
	}
	
	private function get_media_nucleo_comum_2trim() {
		throw Exception("Não implementado");
	}
	
	private function get_media_nucleo_comum_3trim() {
		throw Exception("Não implementado");
	}
	
	
	public function get_faltas($aluno, $turma, $disciplina, $trimestre) {
		
		
		$vetor_todas_faltas = $this->get_vetor_todas_faltas($turma);
		//dd($vetor_todas_faltas);
		$qtde_faltas = null;
		foreach ($vetor_todas_faltas as $falta) {

			if(
				$falta['fk_aluno'] == $aluno &&
				$falta['fk_turma'] == $turma &&
				$falta['trimestre'] == $trimestre &&
				$falta['id_disciplina'] == $disciplina
				) {
					$qtde_faltas = $falta['faltas'];
					break;
				}
		}
		
		
		if($qtde_faltas !== null) {
			return $qtde_faltas;
		} else {
			return $this->sem_valor;
		}
		
	}
	
	
	public static function get_vetor_media_turma($turma_id) {
		
	}
	
	
	public function get_media_turma($turma_id, $disciplina, $trimestre, $arredondar = true) {
	
		$qtde_alunos_encontrados = 0;
		$soma_medias_trimestrais_todos_alunos = 0;
		
		$q = (new SI_TurmaModel())->query(
											"select id_nivel from si_turma where id = $turma_id"
									)->getResultArray();	
									
											
		
		//$q = "SELECT id_nivel FROM si_turma WHERE id = $turma_id";
		//$r = $this->conn->qcv ( $q, "id_nivel" );
		$r = $q;
		
		$nivel = $r [0]['id_nivel'];
		
		
		// consulta SQL verificando se o aluno ainda pertence à turma:
		$q = (new SI_NotaModel())->query(
											"
											SELECT n.fk_aluno
											FROM si_nota n
											
											JOIN si_aluno a
											ON n.fk_aluno = a.id
											
											WHERE
											a.status = '1' 
											AND trimestre = $trimestre
											AND id_disciplina = '$disciplina'
											AND fk_turma = $turma_id
											
											AND (
													SELECT COUNT(*) qtde 
													FROM si_aluno_turma at 
													WHERE at.fk_aluno = n.fk_aluno 
													AND at.fk_turma = n.fk_turma
												) > 0
											
											GROUP BY fk_aluno
										"
										)->getResultArray();									
		
		$raluno = $q;
		
		if ($raluno != null) {
			
			foreach ( $raluno as $alun_id ) {

				// média trimestral de apenas 1 aluno:
				$mt = $this->get_media_trimestral_periodo_anual ( $nivel, $alun_id['fk_aluno'], $disciplina, $turma_id, $trimestre );
				if ($mt == $this->sem_valor) {
					// passa para o próximo aluno, pois este está com alguma nota faltando.
					continue;
				}
				//dd($mt);
				$soma_medias_trimestrais_todos_alunos += (float)$mt;
				$qtde_alunos_encontrados ++;
			}
		} else {
			return $this->sem_valor;
		}
		if ($qtde_alunos_encontrados == 0) {
			return $this->sem_valor;
		}
		$media_turma = $soma_medias_trimestrais_todos_alunos / $qtde_alunos_encontrados;
		

		if($arredondar) {
			$media_turma = calculo::arredonda_nota ( $media_turma, false );
		}
		return $media_turma;
	}
	
	
	
	
	
	
}

















?>
