<?php

namespace App\Console\Commands;

use App\Attendance;
use App\Casteller;
use App\Colla;
use App\Event;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CreateIntegrationFixture extends Command
{
    protected $signature = 'fempinya:create-integration-fixture {email} {password}';

    protected $description = 'Create deterministic local data for API, Flutter integration tests, and manual development';

    public function handle(): int
    {
        if (! Colla::query()->where('shortname', 'integration')->exists()) {
            Artisan::call('fempinya:create-colla', [
                'name' => 'integration',
                'email' => $this->argument('email'),
                'password' => $this->argument('password'),
                '--super-admin' => true,
            ]);
        }

        $colla = Colla::query()->where('shortname', 'integration')->firstOrFail();
        $casteller = Casteller::query()->firstOrCreate(
            ['colla_id' => $colla->getId(), 'alias' => 'integration-casteller'],
            ['status' => 2, 'language' => 'ca', 'name' => 'Integration']
        );

        $supportingCastellers = [
            Casteller::query()->firstOrCreate(
                ['colla_id' => $colla->getId(), 'alias' => 'laia-integration'],
                ['status' => 2, 'language' => 'ca', 'name' => 'Laia']
            ),
            Casteller::query()->firstOrCreate(
                ['colla_id' => $colla->getId(), 'alias' => 'marc-integration'],
                ['status' => 2, 'language' => 'ca', 'name' => 'Marc']
            ),
            Casteller::query()->firstOrCreate(
                ['colla_id' => $colla->getId(), 'alias' => 'aina-integration'],
                ['status' => 2, 'language' => 'ca', 'name' => 'Aina']
            ),
        ];

        $legacyEvent = Event::query()
            ->where('colla_id', $colla->getId())
            ->where('name', 'Integration event')
            ->first();
        if ($legacyEvent) {
            $legacyEvent->setAttribute('name', 'Assaig general');
            $legacyEvent->save();
        }

        $today = Carbon::now()->startOfDay();
        $events = [
            [
                'name' => 'Assaig general',
                'start' => $today->copy()->addDay()->setTime(19, 30),
                'duration' => 120,
                'type' => 1,
                'address' => 'Local de la colla',
                'comments' => 'Assaig de pinya i proves de castells.',
                'companions' => true,
                'status' => 1,
                'companionsCount' => 1,
            ],
            [
                'name' => 'Actuació a la plaça',
                'start' => $today->copy()->addDays(3)->setTime(12, 0),
                'duration' => 150,
                'type' => 2,
                'address' => 'Plaça Major',
                'comments' => 'Actuació conjunta amb les colles convidades.',
                'companions' => true,
                'status' => 3,
            ],
            [
                'name' => 'Sopar de colla',
                'start' => $today->copy()->addDays(6)->setTime(21, 0),
                'duration' => 180,
                'type' => 3,
                'address' => 'Restaurant del Centre',
                'comments' => 'Trobada informal oberta a familiars.',
                'companions' => true,
                'status' => 2,
            ],
            [
                'name' => 'Assaig de canalla',
                'start' => $today->copy()->addDays(8)->setTime(18, 0),
                'duration' => 90,
                'type' => 1,
                'address' => 'Local de la colla',
                'comments' => 'Assaig específic de la canalla.',
                'companions' => false,
                'status' => null,
            ],
            [
                'name' => 'Diada de diumenge',
                'start' => $today->copy()->addDays(11)->setTime(11, 30),
                'duration' => 180,
                'type' => 2,
                'address' => 'Plaça de la Vila',
                'comments' => 'Diada principal del cap de setmana.',
                'companions' => false,
                'status' => 1,
            ],
        ];

        foreach ($events as $index => $definition) {
            $event = $this->upsertEvent($colla, $definition);

            if ($definition['status'] !== null) {
                Attendance::setStatus($casteller->getId(), $event->getId(), $definition['status']);
            }
            if (isset($definition['companionsCount'])) {
                Attendance::setCompanions($casteller->getId(), $event->getId(), $definition['companionsCount']);
            }

            foreach ($supportingCastellers as $supportingCasteller) {
                Attendance::setStatus(
                    $supportingCasteller->getId(),
                    $event->getId(),
                    (($index + $supportingCasteller->getId()) % 3) + 1
                );
            }
        }

        $this->line('Integration fixture ready: casteller '.$casteller->getId().', 4 castellers, 5 upcoming events.');
        return self::SUCCESS;
    }

    private function upsertEvent(Colla $colla, array $definition): Event
    {
        $event = Event::query()
            ->where('colla_id', $colla->getId())
            ->where('name', $definition['name'])
            ->first() ?? new Event();

        $startDate = $definition['start'];
        $event->setAttribute('colla_id', $colla->getId());
        $event->setAttribute('name', $definition['name']);
        $event->setAttribute('start_date', $startDate);
        $event->setAttribute('open_date', Carbon::now()->subDay());
        $event->setAttribute('close_date', $startDate->copy()->addMinutes($definition['duration']));
        $event->setAttribute('duration', $definition['duration']);
        $event->setAttribute('type', $definition['type']);
        $event->setAttribute('address', $definition['address']);
        $event->setAttribute('comments', $definition['comments']);
        $event->setAttribute('companions', $definition['companions']);
        $event->setAttribute('visibility', 1);
        $event->save();

        return $event;
    }
}
