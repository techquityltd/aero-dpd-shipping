<?php

namespace Techquity\ClickDrop\Drivers;

use Aero\Fulfillment\Contracts\Response;
use Aero\Fulfillment\FulfillmentDriver;
use Aero\Fulfillment\Models\Fulfillment;
use Aero\Fulfillment\Responses\FulfillmentResponse;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use League\Csv\Writer;
use League\Flysystem\Filesystem;
use Carbon\Carbon;

abstract class DpdDriver extends FulfillmentDriver
{
    /**
     * Array to be held for the CSV.
     */
    protected $csv;

    /**
     * @var Processing rules for each CSV column
     */
    protected $rules = [
        'address_1' => 35,
        'address_2' => 35,
        'con_value' => 10,
        'contact' => 35,
        'country' => 2,
        'county' => 35,
        'customer_code' => 20,
        'customer_delivery_notes' => 50,
        'description' => 25,
        'email' => 50,
        'height' => 2,
        'instructions' => 50,
        'instructions_2' => 25,
        'insurance' => 1,
        'insured_value' => 2,
        'interlink_job_type_id' => 1,
        'job_reference' => 25,
        'length' => 2,
        'mobile' => 50,
        'name' => 35,
        'postcode' => 8,
        'quantity_of_labels' => 3,
        'reverse_it_reference' => 2,
        'service_code' => 3,
        'sms' => 15,
        'telephone' => 15,
        'timeslot' => 1,
        'town' => 35,
        'user_field_1' => 2,
        'user_field_2' => 2,
        'user_field_3' => 2,
        'user_field_4' => 2,
        'vat_number' => 2,
        'weight' => 4,
        'width' => 2,
        'service_name' => 3,
        'delivery_date' => 10,
        'guaranteed_saturday_delivery' => 1,
    ];

    /**
     * The default fulfillment state of a fulfillment when created.
     */
    public function getDefaultState(): string
    {
        return Fulfillment::PENDING;
    }

    /**
     * Make the fulfillment request.
     *
     * @return \Aero\Fulfillment\Contracts\Response
     */
    public function handle(): Response
    {
        $this->csv = Writer::createFromString('');

        $this->csv->setNewLine(chr('13').chr('10'));

        $headers = $this->getHeader();

        $this->csv->insertOne($headers);

        $reference = with($this->fulfillments, function (Collection $fulfillments) {
            return $fulfillments->map(function (Fulfillment $fulfillment) {
                $this->csv->insertOne($this->processItem($fulfillment));

                return $fulfillment->reference;
            })->unique()->implode('-');
        });

        $this->storeCSV(
            $this->csv->getContent(),
            $reference
        );

        $response = new FulfillmentResponse();

        $response->setSuccessful(true);

        $response->setRedirect(route(config('aero-dpd.redirect_route')));

        return $response;
    }

    /**
     * Process our fulfillment item.
     *
     * @return array
     */
    protected function processItem(Fulfillment $fulfillment)
    {
        $consignment = collect([
            'job_reference' => $fulfillment->reference,
            'interlink_job_type_id' => $fulfillment->job_type_id, // LOOK AT ME FROM AVAILABLE SERVICES? ANOTHER CALL
            'customer_code' => '',
            'name' => $fulfillment->address->full_name,
            'address_1' => $fulfillment->address->line_1,
            'address_2' => $fulfillment->address->line_2,
            'town' => $fulfillment->address->city,
            'county' => $fulfillment->address->zone,
            'country' => $fulfillment->address->country_code,
            'postcode' => $fulfillment->address->postcode,
            'email' => $fulfillment->email,
            'sms' => $fulfillment->address->mobile ?? '',
            'telephone' => $fulfillment->address->mobile ?? $fulfillment->address->phone,
        ]);

        $consignment = $this->sanitiseResponse($consignment);

        return $consignment->values()->toArray();
    }

    /**
     * Remove illegal characters, and trim based on ruleset.
     *
     * @param \Illuminate\Support\Collection $response
     *
     * @return \Illuminate\Support\Collection
     */
    protected function sanitiseResponse($response)
    {
        $rules = $this->rules;

        return $response->each(function ($item, $key) use ($rules) {
            // Remove illegal characters from the label
            $item = str_replace(['+', ',', '/', '\\', '*', '"', '\''], '', $item);

            try {
                $response = substr($item, 0, $rules[$key]);
                return substr($item, 0, $rules[$key]);
            } catch (Exception $e) {
                Log::error('Error when sanitising csv data: {$key}');
                return $item;
            }

        });
    }

    /**
     * Get the CSV Header.
     *
     * @return array
     */
    protected function getHeader()
    {
        return [
            'job_reference',
            'interlink_job_type_id',
            'customer_code',
            'name',
            'address_1',
            'address_2',
            'town',
            'county',
            'country',
            'postcode',
            'email',
            'sms',
            'telephone',
            'instructions',
            'instructions_2',
            'quantity_of_labels',
            'service_code',
            'service_name',
            'delivery_date',
            'guaranteed_saturday_delivery',
            'weight',
            'width',
            'height',
        ];
    }

    /**
     * Save the CSV.
     *
     * @return string
     */
    protected function storeCSV(string $csv, string $reference): void
    {

    }

}
