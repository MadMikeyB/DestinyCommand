<?php

namespace Tests\Unit;

use App\Destiny\CharacterProfileValue;
use App\Destiny\CharacterProgressionValue;
use PHPUnit\Framework\TestCase;

class DestinyValueObjectsTest extends TestCase
{
    public function test_it_uses_enum_backed_titles_for_character_profile_values(): void
    {
        $value = new CharacterProfileValue('light', 2000, 671679327);

        $this->assertSame('Power level', $value->title);
    }

    public function test_it_uses_enum_backed_titles_for_character_progression_values(): void
    {
        $value = new CharacterProgressionValue(2093709363, 1, 671679327);

        $this->assertSame('Flawless', $value->title);
    }
}
