<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Tests\Unit\SortedList;

use HrjSnz\SortedLinkedList\Comparator\LocaleStringComparator;
use HrjSnz\SortedLinkedList\SortedList\StringSortedLinkedList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(StringSortedLinkedList::class)]
#[Group('locale')]
final class StringSortedLinkedListLocaleTest extends TestCase
{
    use StringSortedLinkedListTestCaseTrait;

    protected function createComparator(bool $caseSensitive = true): LocaleStringComparator
    {
        return new LocaleStringComparator('en_US');
    }


    #[Test]
    #[TestDox('Locale comparator handles special characters correctly')]
    public function locale_handlesSpecialCharacters(): void
    {
        $list = $this->createList();
        $list->addMultiple('foo-bar', 'foo_bar', 'foobar');

        $values = iterator_to_array($list);

        $this->assertCount(3, $values);
        $this->assertContains('foo-bar', $values);
        $this->assertContains('foo_bar', $values);
        $this->assertContains('foobar', $values);
    }

    #[Test]
    #[TestDox('Locale comparator handles accented characters correctly')]
    public function locale_handlesAccentedCharacters(): void
    {
        $list = $this->createList();
        $list->addMultiple('café', 'cote', 'côte', 'coté');

        $values = iterator_to_array($list);

        $this->assertContains('café', $values);
        $this->assertContains('cote', $values);
        $this->assertContains('côte', $values);
    }

    #[Test]
    #[TestDox('German locale treats ß appropriately')]
    public function germanLocale_handlesEszett(): void
    {
        if (!\in_array('de_DE', \ResourceBundle::getLocales(''))) {
            $this->markTestSkipped('German locale not available');
        }

        $list = new StringSortedLinkedList(
            new LocaleStringComparator('de_DE')
        );

        $list->addMultiple('straße', 'strasse', 'apollo');

        $values = iterator_to_array($list);

        $this->assertContains('straße', $values);
        $this->assertContains('strasse', $values);
    }

    #[Test]
    #[TestDox('Swedish locale handles åäö at end of alphabet')]
    public function swedishLocale_handlesSwedishCharacters(): void
    {
        if (!\in_array('sv_SE', \ResourceBundle::getLocales(''))) {
            $this->markTestSkipped('Swedish locale not available');
        }

        $list = new StringSortedLinkedList(
            new LocaleStringComparator('sv_SE')
        );

        $list->addMultiple('apple', 'öron', 'zebra', 'åland', 'äpple');

        $values = iterator_to_array($list);

        $this->assertContains('åland', $values);
        $this->assertContains('äpple', $values);
        $this->assertContains('öron', $values);
    }

    #[Test]
    #[TestDox('Turkish locale handles dotted and dotless i')]
    public function turkishLocale_handlesDottedI(): void
    {
        if (!\in_array('tr_TR', \ResourceBundle::getLocales(''))) {
            $this->markTestSkipped('Turkish locale not available');
        }

        $list = new StringSortedLinkedList(
            new LocaleStringComparator('tr_TR')
        );

        $list->addMultiple('İstanbul', 'Izmir', 'istanbul', 'izmir');

        $values = iterator_to_array($list);

        $this->assertContains('İstanbul', $values);
        $this->assertContains('Izmir', $values);
    }

    #[Test]
    #[TestDox('Japanese locale handles Hiragana and Katakana')]
    public function japaneseLocale_handlesJapaneseScripts(): void
    {
        if (!\in_array('ja_JP', \ResourceBundle::getLocales(''))) {
            $this->markTestSkipped('Japanese locale not available');
        }

        $list = new StringSortedLinkedList(
            new LocaleStringComparator('ja_JP')
        );

        $list->addMultiple('あ', 'い', 'う', 'ア', 'イ');

        $values = iterator_to_array($list);

        $this->assertContains('あ', $values);
        $this->assertContains('ア', $values);
    }
}
