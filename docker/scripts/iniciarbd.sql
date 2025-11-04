-- iniciarbd.sql
CREATE ROLE ecidade WITH SUPERUSER LOGIN PASSWORD 'ecidade';
CREATE ROLE plugin WITH LOGIN PASSWORD 'plugin';
CREATE ROLE dbseller WITH LOGIN PASSWORD 'dbseller';
CREATE ROLE dbportal WITH LOGIN PASSWORD 'dbportal';
CREATE ROLE contass WITH LOGIN PASSWORD 'contass';
CREATE ROLE usersrole WITH LOGIN PASSWORD 'usersrole';
CREATE DATABASE ecidade OWNER ecidade;

-- Configurar search_path para todos os roles do e-Cidade
ALTER ROLE ecidade SET search_path = public, sicom, recursoshumanos, site, empenho, diversos, issqn, secretariadeeducacao, tfd, arrecadacao, agua, ouvidoria, acordos, laboratorio, cadastro, habitacao, vacinas, inflatores, escola, juridico, notificacoes, orcamento, licitacao, projetos, divida, protocolo, itbi, material, merenda, pessoal, prefeitura, agendamento, veiculos, tributario, recursoshumanos, dbpref, farmacia, marcas, cemiterio, transporteescolar, ambulatorial, patrimonio, gestorbi, caixa, fiscal, biblioteca, configuracoes, contabilidade, esocial, compras, contrib, social, custos;

ALTER ROLE dbportal SET search_path = public, sicom, recursoshumanos, site, empenho, diversos, issqn, secretariadeeducacao, tfd, arrecadacao, agua, ouvidoria, acordos, laboratorio, cadastro, habitacao, vacinas, inflatores, escola, juridico, notificacoes, orcamento, licitacao, projetos, divida, protocolo, itbi, material, merenda, pessoal, prefeitura, agendamento, veiculos, tributario, recursoshumanos, dbpref, farmacia, marcas, cemiterio, transporteescolar, ambulatorial, patrimonio, gestorbi, caixa, fiscal, biblioteca, configuracoes, contabilidade, esocial, compras, contrib, social, custos;

ALTER ROLE dbseller SET search_path = public, sicom, recursoshumanos, site, empenho, diversos, issqn, secretariadeeducacao, tfd, arrecadacao, agua, ouvidoria, acordos, laboratorio, cadastro, habitacao, vacinas, inflatores, escola, juridico, notificacoes, orcamento, licitacao, projetos, divida, protocolo, itbi, material, merenda, pessoal, prefeitura, agendamento, veiculos, tributario, recursoshumanos, dbpref, farmacia, marcas, cemiterio, transporteescolar, ambulatorial, patrimonio, gestorbi, caixa, fiscal, biblioteca, configuracoes, contabilidade, esocial, compras, contrib, social, custos;

ALTER ROLE plugin SET search_path = public, sicom, recursoshumanos, site, empenho, diversos, issqn, secretariadeeducacao, tfd, arrecadacao, agua, ouvidoria, acordos, laboratorio, cadastro, habitacao, vacinas, inflatores, escola, juridico, notificacoes, orcamento, licitacao, projetos, divida, protocolo, itbi, material, merenda, pessoal, prefeitura, agendamento, veiculos, tributario, recursoshumanos, dbpref, farmacia, marcas, cemiterio, transporteescolar, ambulatorial, patrimonio, gestorbi, caixa, fiscal, biblioteca, configuracoes, contabilidade, esocial, compras, contrib, social, custos;
