import {Button, FormLayout, Frame, Layout, Page, RangeSlider, Toast} from "@shopify/polaris";
import {useCallback, useState} from "react";
import ValidationErrorBanner from "./ValidationErrorBanner.jsx";
import DeleteFakeDataButton from "./DeleteFakeDataButton.jsx";
import useGenerateFakeData from "../hooks/useGenerateFakeData";

const FakeDataCreator = () => {
    const [options, setOptions] = useState({
        productsCount: 0,
        customersCount: 0
    })
    const {
        generate,
        loading: creatingProducts,
        toastMessage,
        errors,
        dismissToast,
        dismissErrors
    } = useGenerateFakeData()

    const handleCountChange = useCallback(
        (value, name) => setOptions(prevOptions => ({...prevOptions, [name]: value})),
        []
    )


    return (
        <Frame>
            <Page title="Generate Fake Data" primaryAction={<DeleteFakeDataButton/>}>
                <Layout>
                    <Layout.Section>
                        <FormLayout>
                            <RangeSlider
                                label={`Number of Products ${options.productsCount > 0 ? '(' + options.productsCount +')' : ''}`}
                                min={0}
                                max={10}
                                step={1}
                                value={options.productsCount}
                                onChange={handleCountChange}
                                id="productsCount"
                            />
                            <RangeSlider
                                label={`Number of Customers ${options.customersCount > 0 ? '(' + options.customersCount +')' : ''}`}
                                min={0}
                                max={10}
                                step={1}
                                value={options.customersCount}
                                onChange={handleCountChange}
                                id="customersCount"
                            />

                            <Button primary size={"large"} loading={creatingProducts}
                                    onClick={() => generate(options)}>Generate</Button>

                            {toastMessage && <Toast content={toastMessage} onDismiss={dismissToast}/>}

                            {errors?.length && (
                                <ValidationErrorBanner
                                    title="Failed to Generate Fake Data"
                                    errors={errors}
                                    onDismiss={dismissErrors}
                                />
                            )}
                        </FormLayout>
                    </Layout.Section>
                </Layout>
            </Page>
        </Frame>
    )
}
export default FakeDataCreator
