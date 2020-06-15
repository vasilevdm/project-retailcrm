<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('home/index.html.twig');
    }

    /**
     * @Route("/fill", name="fill")
     */
    public function fillWithData()
    {
        $clients = [
            [
                'name' => 'Test Client 1',
                'phone' => '+7111111111',
                'email' => 'test@test.ru',
                'addresses' => [
                    [
                        'city' => 'Moscow',
                        'address' => 'Sovetskaya 1'
                    ],
                    [
                        'city' => 'Moscow',
                        'address' => 'Lenina 1'
                    ]
                ]
            ],
            [
                'name' => 'Test Client 2',
                'phone' => '+7222222222',
                'email' => 'test2@test.ru',
                'addresses' => [
                    [
                        'city' => 'Kazan',
                        'address' => 'Sovetskaya 2'
                    ],
                    [
                        'city' => 'N.Novgorod',
                        'address' => 'Lenina 2'
                    ]
                ]
            ],
            [
                'name' => 'Test Client 3',
                'phone' => '+7333333333',
                'email' => 'test3@test.ru',
                'addresses' => [
                    [
                        'city' => 'Novosibirsk',
                        'address' => 'Sovetskaya 3'
                    ],
                    [
                        'city' => 'Novosibirsk',
                        'address' => 'Lenina 3'
                    ]
                ]
            ],
            [
                'name' => 'Test Client 4',
                'phone' => '+7444444444',
                'email' => 'test4@test.ru',
                'addresses' => [
                    [
                        'city' => 'Moscow',
                        'address' => 'Sovetskaya 4'
                    ],
                    [
                        'city' => 'S.Petersburg',
                        'address' => 'Lenina 4'
                    ]
                ]
            ],
            [
                'name' => 'Test Client 5',
                'phone' => '+7555555555',
                'email' => 'test5@test.ru',
                'addresses' => [
                    [
                        'city' => 'Tula',
                        'address' => 'Sovetskaya 5'
                    ],
                    [
                        'city' => 'Khimki',
                        'address' => 'Lenina 5'
                    ]
                ]
            ]
        ];

        $entityManager = $this->getDoctrine()->getManager();

        foreach ($clients as $clientData) {
            $client = new Client();
            $client->setName($clientData['name']);
            $client->setPhone($clientData['phone']);
            $client->setEmail($clientData['email']);
            foreach ($clientData['addresses'] as $addressData) {
                $address = new Address();
                $address->setCity($addressData['city']);
                $address->setAddress($addressData['address']);
                $client->addAddress($address);
            }
            $entityManager->persist($client);
            $entityManager->flush();
        }


        return $this->redirectToRoute('home');
    }
}
