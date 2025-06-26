import { registerPaymentMethod } from '@woocommerce/blocks-registry';
import { getSetting } from '@woocommerce/settings';

const settings = getSetting('garantibbva_data', {});

const GarantiBBVAComponent = ({ eventRegistration, emitResponse }) => {
    const { onPaymentProcessing } = eventRegistration;

    useEffect(() => {
        const unsubscribe = onPaymentProcessing(() => {
            
            emitResponse.responseSuccess();
        
        });
        return () => unsubscribe();
    }, [onPaymentProcessing]);

    return (
        <div>
            <p>{settings.description}</p>
            {/*block-integration */}
        </div>
    );
};

registerPaymentMethod({
    name: 'garanti-payment-module',
    label: settings.title,
    content: <GarantiBBVAComponent />,
    edit: <GarantiBBVAComponent />,
    canMakePayment: () => true,
    ariaLabel: settings.title,
    supports: {
        features: settings.supports,
    },
});

