@extends('layouts.app')

@section('title', 'Política de Privacidade - Monitor de Preços')
@section('description', 'Conheça a política de privacidade e saiba como o Monitor de Preços coleta, utiliza e protege seus dados pessoais, em conformidade com a LGPD.')

@section('content')
    <div class="bg-gray-50 min-h-screen py-10">
        <div class="container mx-auto px-4 max-w-4xl">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Política de Privacidade</h1>
                <p class="mt-2 text-gray-500 text-sm">Última atualização: {{ \Carbon\Carbon::parse('2026-04-12')->format('d/m/Y') }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-8 space-y-8 text-gray-700 leading-relaxed">

                <section class="mt-4">
                    <p>
                        O <strong>Monitor de Preços</strong> tem o compromisso de respeitar sua privacidade e garantir o sigilo de todas as informações que você nos fornece por meio do nosso site.
                        Esta Política de Privacidade descreve, de forma clara e transparente, como tratamos os dados pessoais dos nossos usuários, em conformidade com a
                        <strong>Lei Geral de Proteção de Dados Pessoais (LGPD — Lei nº 13.709/2018)</strong>.
                    </p>
                    <p class="mt-4">
                        A navegação e o uso deste site implicam que o usuário está ciente dos termos aqui descritos e concorda com eles.
                    </p>
                </section>

                <section class="mt-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">1. Quem somos</h2>
                    <p class="mt-4">
                        O Monitor de Preços é uma plataforma de publicidade e comparação de preços de produtos de lojas virtuais de todo o Brasil.
                        Somos um <strong>site de publicidade gratuito</strong>: não vendemos produtos diretamente, não processamos pagamentos e não intermediamos transações comerciais entre usuários e lojas.
                        Nossa receita é gerada por <strong>comissões em acordos comerciais com algumas varejistas parceiras</strong>, quando o usuário é direcionado ao site da loja a partir do nosso portal.
                    </p>
                    <p class="mt-4">
                        <strong>Group 1245 LTDA</strong> — CNPJ: 52.171.773/0001-34.
                    </p>
                </section>

                <section class="mt-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">2. Dados coletados e finalidades</h2>
                    <p class="mt-4">
                        Coletamos apenas os dados estritamente necessários para o funcionamento da plataforma e para a melhoria da sua experiência:
                    </p>
                    <ul class="mt-4 space-y-3 list-disc list-inside">
                        <li>
                            <strong>Dados de cadastro:</strong> nome, endereço de e-mail e senha, utilizados para criar e gerenciar sua conta, bem como para enviar alertas de preço e comunicações sobre promoções, caso você autorize.
                        </li>
                        <li>
                            <strong>Dados de navegação:</strong> páginas acessadas, produtos visualizados, buscas realizadas e histórico de navegação, utilizados para personalizar a experiência, exibir conteúdo relevante e gerar estatísticas de uso.
                        </li>
                        <li>
                            <strong>Dados de preferências:</strong> produtos favoritados e configurações de alertas, armazenados para viabilizar os recursos da conta.
                        </li>
                        <li>
                            <strong>Dados técnicos:</strong> endereço IP, tipo de navegador, sistema operacional e identificadores de dispositivo, coletados automaticamente para fins de segurança, diagnóstico técnico e análise de desempenho.
                        </li>
                    </ul>
                </section>

                <section class="mt-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">3. Cookies</h2>
                    <p class="mt-4">
                        Utilizamos cookies para melhorar sua experiência de navegação. Cookies são pequenos arquivos de dados transferidos pelo site para o seu dispositivo.
                        <strong>Declaramos expressamente que não utilizamos drop cookie, cookie stuffing ou quaisquer práticas similares de atribuição fraudulenta de cookies.</strong>
                    </p>
                    <p class="mt-4">Os tipos de cookies que utilizamos incluem:</p>
                    <ul class="mt-3 space-y-2 list-disc list-inside">
                        <li><strong>Estritamente necessários:</strong> essenciais para o funcionamento do site, como autenticação e segurança de sessão.</li>
                        <li><strong>Desempenho:</strong> coletam dados agregados sobre como os visitantes navegam no site, ajudando a identificar melhorias.</li>
                        <li><strong>Funcionalidade:</strong> permitem lembrar suas preferências e personalizar sua experiência.</li>
                        <li><strong>Rastreamento de afiliados:</strong> utilizados para atribuir corretamente visitas oriundas do nosso site às lojas parceiras, sem qualquer prática de atribuição fraudulenta.</li>
                    </ul>
                    <p class="mt-4">
                        Você pode gerenciar ou desativar cookies diretamente nas configurações do seu navegador. Note que desativar determinados cookies pode impactar funcionalidades da plataforma.
                    </p>
                </section>

                <section class="mt-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">4. Compartilhamento de dados</h2>
                    <p class="mt-4">
                        O Monitor de Preços não vende dados pessoais a terceiros. O compartilhamento pode ocorrer apenas nas seguintes situações:
                    </p>
                    <ul class="mt-4 space-y-3 list-disc list-inside">
                        <li>
                            <strong>Parceiros de serviço:</strong> prestadores de serviços contratados para hospedagem, análise de dados, envio de e-mails e outros recursos técnicos da plataforma, que tratam os dados exclusivamente para essa finalidade.
                        </li>
                        <li>
                            <strong>Varejistas parceiras:</strong> ao clicar em um link de oferta, você será redirecionado ao site da loja parceira. A partir desse momento, as práticas de privacidade aplicáveis são as da própria loja.
                        </li>
                        <li>
                            <strong>Autoridades competentes:</strong> quando exigido por lei, decisão judicial ou requisição de órgão governamental.
                        </li>
                    </ul>
                </section>

                <section class="mt-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">5. Comunicações por e-mail</h2>
                    <p class="mt-4">
                        Caso você autorize, podemos enviar e-mails com alertas de preço, promoções e novidades.
                        Você pode cancelar o recebimento a qualquer momento acessando as configurações da sua conta ou clicando no link de descadastro presente em todos os e-mails enviados.
                    </p>
                    <p class="mt-4">
                        Nossos e-mails nunca contêm anexos executáveis (.exe, .com, .scr, .bat) nem solicitam sua senha. Em caso de suspeita de e-mail fraudulento, entre em contato conosco pela <a href="{{ route('pages.help-center') }}" class="text-blue-600 hover:underline">Central de Ajuda</a>.
                    </p>
                </section>

                <section class="mt-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">6. Segurança da informação</h2>
                    <p class="mt-4">
                        Adotamos medidas técnicas e organizacionais adequadas para proteger seus dados pessoais contra acesso não autorizado, alteração, divulgação ou destruição. Isso inclui:
                    </p>
                    <ul class="mt-3 space-y-2 list-disc list-inside">
                        <li>Transmissão de dados via protocolo HTTPS com criptografia TLS.</li>
                        <li>Armazenamento seguro de senhas com hashing criptográfico.</li>
                        <li>Controle de acesso restrito aos dados pessoais.</li>
                        <li>Monitoramento contínuo de segurança da infraestrutura.</li>
                    </ul>
                    <p class="mt-4">
                        Nunca divulgue sua senha a terceiros. Em caso de suspeita de uso não autorizado da sua conta, altere sua senha imediatamente em "Minha Conta".
                    </p>
                </section>

                <section class="mt-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">7. Retenção de dados</h2>
                    <p class="mt-4">
                        Armazenamos seus dados pessoais pelo tempo necessário para cumprir as finalidades descritas nesta política ou pelo prazo exigido por lei.
                        Após o encerramento da conta, os dados poderão ser mantidos apenas pelo período prescricional aplicável ou por obrigação legal.
                    </p>
                </section>

                <section class="mt-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">8. Seus direitos como titular dos dados</h2>
                    <p class="mt-4">A LGPD garante aos titulares os seguintes direitos em relação aos seus dados pessoais:</p>
                    <ul class="mt-3 space-y-2 list-disc list-inside">
                        <li>Confirmar a existência de tratamento de dados pessoais.</li>
                        <li>Acessar os dados pessoais que tratamos sobre você.</li>
                        <li>Solicitar a correção de dados incompletos, inexatos ou desatualizados.</li>
                        <li>Solicitar a anonimização, bloqueio ou eliminação de dados desnecessários ou excessivos.</li>
                        <li>Solicitar a portabilidade dos dados a outro fornecedor de serviço.</li>
                        <li>Revogar o consentimento a qualquer momento, quando o tratamento for baseado nele.</li>
                        <li>Solicitar informações sobre o compartilhamento dos seus dados com terceiros.</li>
                    </ul>
                    <p class="mt-4">
                        Para exercer seus direitos, entre em contato conosco pela <a href="{{ route('pages.help-center') }}" class="text-blue-600 hover:underline">Central de Ajuda</a>.
                    </p>
                </section>

                <section class="mt-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">9. Menores de idade</h2>
                    <p class="mt-4">
                        Nossos serviços são destinados a maiores de 18 anos. Não coletamos intencionalmente dados pessoais de menores. Caso identifiquemos o cadastro de um menor sem autorização dos responsáveis, os dados serão removidos imediatamente.
                    </p>
                </section>

                <section class="mt-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">10. Alterações desta política</h2>
                    <p class="mt-4">
                        Esta Política de Privacidade pode ser atualizada periodicamente para refletir mudanças operacionais, legais ou de melhoria da plataforma.
                        Publicaremos sempre a versão mais atualizada nesta página. Para alterações significativas, notificaremos os usuários cadastrados por e-mail.
                    </p>
                </section>

                <section class="mt-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">11. Contato</h2>
                    <p class="mt-4">
                        Em caso de dúvidas sobre esta Política de Privacidade ou sobre o tratamento dos seus dados pessoais, entre em contato com nossa equipe.
                    </p>
                </section>

            </div>
        </div>
    </div>
@endsection
