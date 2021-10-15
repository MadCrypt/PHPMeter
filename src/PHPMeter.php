<?php

 
class PHPMeter
{
    public const EXISTING_STEP_EXCEPTION = "Tried to add a new step with the same label as an already existing step.";

    
    private $steps = [];

    
    public function getTime(): float
    {
        return \microtime(true);
    }

    
    public function getMemory(): int
    {
        return \memory_get_usage();
    }

   
    public function getMemoryPeak(): int
    {
        return \memory_get_peak_usage();
    }

   
    public function getUsage(): array
    {
        return \getrusage();
    }

    
    public function getServerLoad(){
        $str = \substr(strrchr(shell_exec("uptime"),":"),1);
        $avs = \array_map("trim",explode(",",$str));
    
        return $avs;

    }

 
    public function step(string $label): ?array
    {
        if (\array_key_exists($label, $this->steps)) {
            throw new Exception(self::EXISTING_STEP_EXCEPTION, 1);
            return null;
        }

        $step = [
            'time' => $this->getTime(),
            'memory' => $this->getMemory(),
            'peak_memory' => $this->getMemoryPeak(),
            'usage' => $this->getUsage()
        ];

        $this->steps[$label] = $step;

        return $step;
    }

  
    public function getSteps(): array
    {
        return $this->steps;
    }

  
    public function getTimeDifference(string $primaryLabel, string $secondaryLabel): float
    {
        return $this->steps[$secondaryLabel]['time'] - $this->steps[$primaryLabel]['time'];
    }



   
    public function getMemoryDifference(string $primaryLabel, string $secondaryLabel): float
    {
        $memoryDiff = $this->steps[$secondaryLabel]['memory'] - $this->steps[$primaryLabel]['memory'];

        return $memoryDiff;
    }

  
    public function getUsageDifference(string $primaryLabel, string $secondaryLabel): array
    {
        $arr1 = $this->steps[$primaryLabel]['usage'];
        $arr2 = $this->steps[$secondaryLabel]['usage'];

        
        $arr1['ru_utime.tv'] = ($arr1['ru_utime.tv_usec'] / 1000000) + $arr1['ru_utime.tv_sec'];
        $arr2['ru_utime.tv'] = ($arr2['ru_utime.tv_usec'] / 1000000) + $arr2['ru_utime.tv_sec'];

       
        $arr1['ru_stime.tv'] = ($arr1['ru_stime.tv_usec'] / 1000000) + $arr1['ru_stime.tv_sec'];
        $arr2['ru_stime.tv'] = ($arr2['ru_stime.tv_usec'] / 1000000) + $arr2['ru_stime.tv_sec'];

        
        unset(
            $arr1['ru_utime.tv_usec'],
            $arr1['ru_utime.tv_sec'],
            $arr2['ru_utime.tv_usec'],
            $arr2['ru_utime.tv_sec'],
            $arr1['ru_stime.tv_usec'],
            $arr1['ru_stime.tv_sec'],
            $arr2['ru_stime.tv_usec'],
            $arr2['ru_stime.tv_sec']
        );

      
        foreach ($arr1 as $key => $value) {
            $array[$key] = $arr2[$key] - $arr1[$key];
        }

        return $array;
    }

    public  function secToHR($seconds) {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds / 60) % 60);
        $seconds = $seconds % 60;
        return "$hours:$minutes:$seconds";
      }

  
    public function getReport(string $primaryLabel, string $secondaryLabel): array
    {
        $report = [];

      
        $load = $this->getServerLoad();

        $time = $this->getTimeDifference($primaryLabel, $secondaryLabel);
        $usage = $this->getUsageDifference($primaryLabel, $secondaryLabel);
        $memory = $this->getMemoryDifference($primaryLabel, $secondaryLabel);
        $memoryPeak = $this->getMemoryPeak();
        $time = $this->secToHR($time);
     
        $report['Time Taken'] = $time;
        $report['Time taken in User Mode in seconds'] = $usage['ru_utime.tv'] ?? 'Not Available';
        $report['Time taken in System Mode in seconds'] = $usage['ru_stime.tv'] ?? 'Not Available';
        $report['Total time taken in Kernel in seconds'] = $usage['ru_stime.tv'] + $usage['ru_utime.tv'];
        
        $report['Memory limit in MB'] = str_replace('M', '', ini_get('memory_limit'));
        $report['Memory usage in MB'] = $memory / 1024 / 1024 ?? 'Not Available';
        $report['Peak memory usage in MB'] = $memoryPeak / 1024 / 1024 ?? 'Not Available';
        
        $report['Average server load in last minute'] = $load['0'];
        $report['Maximum resident shared size in KB'] = $usage['ru_maxrss'] ?? 'Not Available';
        $report['Integral shared memory size'] = $usage['ru_ixrss'] ?? 'Not Available';
        $report['Integral unshared data size'] = $usage['ru_idrss'] ?? 'Not Available';
        $report['Integral unshared stack size'] = $usage['ru_isrss'] ?? 'Not Available';
        $report['Number of page reclaims'] = $usage['ru_minflt'] ?? 'Not Available';
        $report['Number of page faults'] = $usage['ru_majflt'] ?? 'Not Available';
        $report['Number of block input operations'] = $usage['ru_inblock'] ?? 'Not Available';
        $report['Number of block output operations'] = $usage['ru_outblock'] ?? 'Not Available';
        $report['Number of messages sent'] = $usage['ru_msgsnd'] ?? 'Not Available';
        $report['Number of messages received'] = $usage['ru_msgrcv'] ?? 'Not Available';
        $report['Number of signals received'] = $usage['ru_nsignals'] ?? 'Not Available';
        $report['Number of voluntary context switches'] = $usage['ru_nvcsw'] ?? 'Not Available';
        $report['Number of involuntary context switches'] = $usage['ru_nivcsw'] ?? 'Not Available';

        return $report;
    }
}
