<?php

namespace App\TournamentBundle\Services;
use Doctrine\ORM\EntityManager;
use App\TournamentBundle\Entity\Usercast;

class resultsService
{
	protected $em;

	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	// Расчет результатов по ПРОГРЕССИВНОЙ схеме
	private function annihilation($data){
		# Расскладываем главный счет
		$hline = [];
		# Победитель
		if($data[0] > $data[1]){ 
			$hline[0] = 1;
		} else if($data[0] < $data[1]){ 
			$hline[0] = 2;
		} else if($data[0] == $data[1]){ 
			$hline[0] = 0; }

		# Разница мячей
		$hline[1] = abs($data[0] - $data[1]);

		# Количество голов первой команды
		$hline[2] = $data[0];

		# Сумма голов
		$hline[3] = $data[0] + $data[1];
		
		return $hline;
			
	}

	// Расчет результатов по ПРОГРЕССИВНОЙ схеме
	public function mathem($idfore, $r1, $r2, $fore_end) {

		// получаем все нужные idfore в таблице usercast
		// просчитываем и записываем результаты

		$scores = $this->em->getRepository('AppTournamentBundle:Usercast')->get_scores($idfore);

		for($i=0;$i<count($scores);$i++) {
			$one = $this->annihilation(array($r1, $r2));
			$two = $this->annihilation(array($scores[$i]['result1'], $scores[$i]['result2']));

			if($one == $two){ 
				$result = 5;
			} else if ($one[0] == $two[0] and $one[1] == $two[1] and (abs($one[3] - $two[3]) <= 2)){
				if($one[0] == 0)
					$result = 3;
				else
					$result = 4;
			} else if ($one[0] == $two[0] and $one[1] == $two[1] and (abs($one[3] - $two[3]) > 2)){ 
				$result = 3;
			} else if ($one[0] == $two[0] and (abs($one[1] - $two[1]) <= 1) and (abs($one[3] - $two[3]) <= 1)) {
				$result = 3;
			} else if ($one[0] == $two[0]) {  
				$result = 2;
			} else {
				$result = 0;
			}

			$usercast = $this->em->getRepository('AppTournamentBundle:Usercast')->find($scores[$i]['id']);

			$usercast->setBall($result);

			$this->em->persist($usercast);
		}

		$this->em->flush();


		// return $result; 
	}

	// Расчет результатов по классической схеме
	private function OLDannihilation($data){
		# Расскладываем главный счет
		$hline = [];
		# Победитель
		if($data[0] > $data[1]){ $hline[0] = 1;
		} else if($data[0] < $data[1]){ $hline[0] = 2;
		} else if($data[0] == $data[1]){ $hline[0] = 0; }

		# Разница мячей
		$hline[1] = abs($data[0] - $data[1]);

		# Количество голов первой команды
		$hline[2] = $data[0];
		
		return $hline;
			
	}

	// Расчет результатов по классической схеме
	public function OLDmathem($head, $plus){
		$one = $this->OLDannihilation($head);
		$two = $this->OLDannihilation($plus);

		if($one == $two){ $result = 3;
		} else if ($one[0] == $two[0] and $one[1] == $two[1]){ 
			$result = 2;
			if($one[0] == 0){ $result = 1; }
		} else if ($one[0] == $two[0]){ $result = 1;
		} else { $result = 0;
		}
		return $result; 
	}

}