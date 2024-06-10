<?php
namespace Alexrubl\Daterangefilter;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Http\Requests\NovaRequest;
use InvalidArgumentException;
use Laravel\Nova\Filters\Filter;
use Alexrubl\Daterangefilter\Enums\Config;

class Daterangefilter extends Filter
{
    /**
     * @var string
     */
    public $component = 'daterange-filter';

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    protected $column;

    /**
     * @var array
     */
    protected $config;

    public function __construct(string $name = 'Created at', string $column = Model::CREATED_AT, array $config = [])
    {
        $this->name = isset($this->name) ? $this->name : $name;
        $this->column = $column;
        $this->config = $config;

        $this->configure();
    }

    /**
     * @param Request $request
     * @param Builder $query
     * @param mixed   $value
     *
     * @return Builder
     */
    public function apply(NovaRequest $request, $query, $value)
    {
        info($value);
        $query->whereBetween(
            $this->column,
            [
                Carbon::createFromFormat('Y-m-d', $value[0])->startOfDay(),
                Carbon::createFromFormat('Y-m-d', $value[1])->endOfDay(),
            ]
        );

        return $query;
    }

    protected function configure(): void
    {
        if (empty($this->config)) {
            return;
        }

        foreach ($this->config as $property => $value) {
            if (!in_array($property, Config::getProperties(), true)) {
                throw new InvalidArgumentException('Invalid property: ' . $property);
            }

            $this->withMeta([$property => $value]);
        }
    }

    public function options(NovaRequest $request): array
    {
        return [];
    }

    public function key(): string
    {
        return parent::key() . '_' . $this->column;
    }

    /**
     * @return string[]
     */
    public function default()
    {
        return array_key_exists(Config::DEFAULT_DATE, $this->config)
            ? $this->config[Config::DEFAULT_DATE]
            : [];
    }
}
