@extends('layouts.app')

@section('title', 'Termos de Uso - Monitor de Preços')
@section('description', 'Leia os Termos de Uso do Monitor de Preços e conheça as condições de utilização do site, direitos, responsabilidades e avisos legais.')

@section('content')
    <div class="bg-gray-50 min-h-screen py-10">
        <div class="container mx-auto px-4 max-w-4xl">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Termos de Uso</h1>
                <p class="mt-2 text-gray-500 text-sm">Última atualização: {{ \Carbon\Carbon::parse('2026-04-12')->format('d/m/Y') }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-8 space-y-8 text-gray-700 leading-relaxed">

                <section>
                    <p>
                        Ao acessar e utilizar o site <strong>Monitor de Preços</strong>, você declara ter lido, compreendido e concordado com os presentes Termos de Uso.
                        Caso não concorde com qualquer disposição aqui prevista, pedimos que não utilize nossos serviços.
                    </p>
                    <p class="mt-4">
                        O uso deste site está condicionado à aceitação destes Termos de Uso e da nossa
                        <a href="{{ route('pages.privacy') }}" class="text-blue-600 hover:underline">Política de Privacidade</a>.
                    </p>
                </section>

                <section class="mt-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">1. Sobre o Monitor de Preços</h2>
                    <p class="mt-4">
                        O Monitor de Preços é um <strong>site de publicidade e comparação de preços gratuito</strong>, operado pela <strong>Group 1245 LTDA</strong> (CNPJ: 52.171.773/0001-34).
                        Nossa plataforma agrega e exibe informações de produtos e preços de lojas virtuais parceiras de todo o Brasil, facilitando a pesquisa e comparação pelo usuário.
                    </p>
                    <p class="mt-4">
                        <strong>Não somos uma loja virtual.</strong> Não vendemos produtos, não processamos pagamentos, não emitimos notas fiscais e não somos parte em qualquer transação comercial realizada entre o usuário e as lojas parceiras.
                    </p>
                    <p class="mt-4">
                        Nossa receita é gerada por <strong>comissões em acordos comerciais com algumas varejistas parceiras</strong>, quando o usuário acessa o site da loja a partir do nosso portal. Esse modelo não impacta os preços exibidos nem implica qualquer preferência editorial por determinadas lojas.
                    </p>
                </section>

                <section class="mt-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">2. Aviso sobre preços e disponibilidade</h2>
                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="font-medium text-yellow-800">
                            Preços, condições e disponibilidade dos produtos podem variar a qualquer momento e sem aviso prévio, conforme definição exclusiva de cada loja parceira.
                            <strong>Consulte sempre a loja antes de finalizar sua compra.</strong>
                        </p>
                    </div>
                    <p class="mt-4">
                        As informações exibidas no Monitor de Preços — incluindo preços, fotos, descrições, condições de frete e disponibilidade em estoque — são fornecidas pelas lojas parceiras e atualizadas com a frequência disponível pelas integrações.
                        O Monitor de Preços não se responsabiliza por divergências entre as informações exibidas em nosso site e as praticadas pela loja no momento da compra.
                    </p>
                </section>

                <section class="mt-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">3. Uso da plataforma</h2>
                    <p class="mt-4">Ao utilizar o Monitor de Preços, você se compromete a:</p>
                    <ul class="mt-3 space-y-2 list-disc list-inside">
                        <li>Usar a plataforma para fins lícitos e em conformidade com a legislação brasileira vigente.</li>
                        <li>Não utilizar meios automatizados (bots, scrapers, crawlers) para acessar, coletar ou indexar conteúdo do site sem autorização prévia por escrito.</li>
                        <li>Não tentar comprometer a segurança, integridade ou disponibilidade da plataforma.</li>
                        <li>Fornecer informações verdadeiras no cadastro e mantê-las atualizadas.</li>
                        <li>Manter a confidencialidade das credenciais de acesso à sua conta.</li>
                        <li>Não praticar atos que possam prejudicar outros usuários, lojas parceiras ou a própria plataforma.</li>
                    </ul>
                </section>

                <section class="mt-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">4. Conta de usuário</h2>
                    <p class="mt-4">
                        O cadastro na plataforma é gratuito e opcional. Funcionalidades como favoritos, alertas de preço e histórico de navegação requerem uma conta ativa.
                        Você é inteiramente responsável por todas as atividades realizadas sob sua conta.
                    </p>
                    <p class="mt-4">
                        Reservamo-nos o direito de suspender ou encerrar contas que violem estes Termos de Uso, sem aviso prévio.
                    </p>
                </section>

                <section class="mt-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">5. Propriedade intelectual</h2>
                    <p class="mt-4">
                        Todo o conteúdo original do Monitor de Preços — incluindo marca, logotipo, design, código-fonte e textos editoriais — é de propriedade da <strong>Group 1245 LTDA</strong> e protegido pela legislação de propriedade intelectual.
                        É vedada a reprodução, distribuição ou uso comercial sem autorização prévia por escrito.
                    </p>
                    <p class="mt-4">
                        Imagens, descrições e dados de produtos são de responsabilidade das respectivas lojas parceiras. O Monitor de Preços não reivindica propriedade sobre esse conteúdo.
                    </p>
                </section>

                <section class="mt-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">6. Links para sites de terceiros</h2>
                    <p class="mt-4">
                        O Monitor de Preços direciona usuários a sites de lojas parceiras por meio de links. Esses sites são de responsabilidade exclusiva de seus operadores, possuem políticas de privacidade e termos de uso próprios e operam de forma independente do Monitor de Preços.
                    </p>
                    <p class="mt-4">
                        Não nos responsabilizamos pelo conteúdo, práticas comerciais, segurança ou qualquer dano decorrente do uso de sites de terceiros acessados via nossa plataforma.
                    </p>
                </section>

                <section class="mt-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">7. Limitação de responsabilidade</h2>
                    <p class="mt-4">
                        O Monitor de Preços não se responsabiliza por:
                    </p>
                    <ul class="mt-3 space-y-2 list-disc list-inside">
                        <li>Divergências entre preços exibidos no site e preços praticados pela loja no momento da compra.</li>
                        <li>Indisponibilidade de produtos, alterações de estoque ou cancelamentos realizados pelas lojas.</li>
                        <li>Problemas relacionados à entrega, garantia, atendimento ou qualidade dos produtos adquiridos nas lojas parceiras.</li>
                        <li>Danos decorrentes de interrupções temporárias no serviço por razões técnicas ou de manutenção.</li>
                        <li>Uso indevido das credenciais de acesso pelo próprio usuário ou por terceiros.</li>
                    </ul>
                </section>

                <section class="mt-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">8. Cookies e rastreamento</h2>
                    <p class="mt-4">
                        Utilizamos cookies para melhorar a experiência de navegação e para o correto funcionamento do programa de afiliados com as lojas parceiras.
                        <strong>Declaramos expressamente que não utilizamos práticas de drop cookie, cookie stuffing ou qualquer mecanismo de atribuição fraudulenta de comissões.</strong>
                    </p>
                    <p class="mt-4">
                        Para mais informações, consulte nossa <a href="{{ route('pages.privacy') }}" class="text-blue-600 hover:underline">Política de Privacidade</a>.
                    </p>
                </section>

                <section class="mt-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">9. Alterações dos Termos de Uso</h2>
                    <p class="mt-4">
                        Estes Termos de Uso podem ser atualizados periodicamente para refletir mudanças operacionais, legais ou de melhoria da plataforma.
                        A versão mais atual estará sempre disponível nesta página. Para alterações relevantes, os usuários cadastrados serão notificados por e-mail.
                    </p>
                    <p class="mt-4">
                        A continuidade do uso da plataforma após a publicação de atualizações implica a aceitação dos novos termos.
                    </p>
                </section>

                <section class="mt-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">10. Lei aplicável e foro</h2>
                    <p class="mt-4">
                        Estes Termos de Uso são regidos pela legislação brasileira. As partes elegem o foro da comarca de domicílio do usuário para dirimir quaisquer controvérsias decorrentes deste instrumento, renunciando a qualquer outro, por mais privilegiado que seja.
                    </p>
                </section>

                <section class="mt-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">11. Contato</h2>
                    <p class="mt-4">
                        Para dúvidas, solicitações ou reclamações relacionadas a estes Termos de Uso, entre em contato com nossa equipe.
                    </p>
                </section>

            </div>
        </div>
    </div>
@endsection
