<?php
namespace CroudTech\Repositories\Console\Commands;

use Illuminate\Console\Command;

class CreateRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ct-repositories:create
        { model-name : The fully qualified class name of the model }
        { repository-name? : Specify a custom repository name }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Repository';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $repository_generator = new \CroudTech\Repositories\RepositoryGenerator($this->argument('model-name'), $this->argument('repository-name'));

        try {
            if ($repository_generator->generateRepository()) {
                $this->info(sprintf('Generated %s repository', $repository_generator->getFullRepositoryPath()));
                $this->info(sprintf('Using %s contract', $repository_generator->getFullContractPath()));
            } else {
                $this->error('Repository generation failed.');
            }
        } catch (\Exception $e) {
            $this->error('There was an error when generating the repository. Please check your error log.');
            \Illuminate\Support\Facades\Log::error($e);
        }
    }
}
